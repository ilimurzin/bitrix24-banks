<?php

declare(strict_types=1);

namespace App\Controller;

use Bitrix24\SDK\Core\Credentials\ApplicationProfile;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InstallController extends AbstractController
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $scope,
        private readonly string $userTypeId,
        private readonly string $handlerUrl,
    ) {}

    #[Route(
        path: 'install',
        name: 'install',
        methods: 'POST',
    )]
    public function install(Request $request): Response
    {
        $serviceBuilder = ServiceBuilderFactory::createServiceBuilderFromPlacementRequest(
            $request,
            new ApplicationProfile(
                $this->clientId,
                $this->clientSecret,
                Scope::initFromString($this->scope),
            ),
        );

        $applicationId = $serviceBuilder->getMainScope()->main()->getApplicationInfo()->applicationInfo()->ID;
        $userTypeId = $applicationId . '_' . $this->userTypeId;
        $userTypeInstalled = false;

        foreach ($serviceBuilder->getPlacementScope()->userfieldtype()->list()->getUserFieldTypes() as $type) {
            if ($type->USER_TYPE_ID === $userTypeId) {
                $userTypeInstalled = true;
            }
        }

        if (!$userTypeInstalled) {
            $serviceBuilder->getPlacementScope()->userfieldtype()->add(
                $userTypeId,
                $this->handlerUrl,
                'Банк',
                'Справочник банков',
            );
        }

        $types = ['contact', 'lead', 'company', 'deal'];
        $fieldName = mb_strtoupper($userTypeId);

        foreach ($types as $type) {
            $response = $serviceBuilder->core->call(
                "crm.{$type}.userfield.list",
                [
                    'filter' => [
                        'FIELD_NAME' => 'UF_CRM_' . $fieldName,
                    ],
                ],
            );

            if (!$response->getResponseData()->getResult()) {
                $serviceBuilder->core->call(
                    "crm.{$type}.userfield.add",
                    [
                        'fields' => [
                            'USER_TYPE_ID' => $userTypeId,
                            'FIELD_NAME' => $fieldName,
                            'LABEL' => 'Банк',
                        ],
                    ],
                );
            }
        }

        return $this->render('install.html.twig');
    }
}
