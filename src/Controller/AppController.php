<?php

declare(strict_types=1);

namespace App\Controller;

use Bitrix24\SDK\Application\Requests\Placement\PlacementRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'handler',
        methods: 'POST',
    )]
    public function handle(Request $request): Response
    {
        $placementRequest = new PlacementRequest($request);

        if ($placementRequest->getCode() === 'USERFIELD_TYPE') {
            $mode = $placementRequest->getPlacementOptions()['MODE'] ?? '';

            if ($mode === 'view') {
                return $this->render('uf/view.html.twig', [
                    'banks' => file_get_contents(__DIR__ . '/../../public/v1/banks.json'),
                ]);
            }

            if ($mode === 'edit') {
                return $this->render('uf/edit.html.twig', [
                    'banks' => file_get_contents(__DIR__ . '/../../public/v1/banks.json'),
                ]);
            }

            throw new BadRequestHttpException("Unsupported MODE `{$mode}`");
        }

        return $this->render('index.html.twig');
    }

    #[Route(
        path: '/',
        methods: ['HEAD', 'GET'],
    )]
    public function stub(): Response
    {
        return new Response('For Bitrix');
    }
}
