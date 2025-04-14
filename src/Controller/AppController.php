<?php

declare(strict_types=1);

namespace App\Controller;

use Bitrix24\SDK\Application\Requests\Placement\PlacementRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        return $this->render('index.html.twig', [
            'code' => $placementRequest->getCode(),
            'domain_url' => $placementRequest->getDomainUrl(),
        ]);
    }
}
