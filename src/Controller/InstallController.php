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

        $serviceBuilder->getPlacementScope()->userfieldtype()->add(
            $this->userTypeId,
            $this->handlerUrl,
            'Банк',
            'Справочник банков',
        );

        return new Response(
            content: <<<'HTML'
                <!doctype html>
                <html lang="ru">
                <head>
                <title>Справочник банков</title>
                <script src="//api.bitrix24.com/api/v1/"></script>
                </head>
                <body>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    BX24.init(function() {
                        BX24.installFinish()
                    })
                })
                </script>
                </body>
                </html>
                HTML,
        );
    }
}
