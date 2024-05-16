<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Order\Service\OrderQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route(path: '/order/{id<\d+>}', name: 'query-single-order', methods: ['GET'], format: 'json')]
    public function getOrder(int $id, OrderQuery $provider): JsonResponse
    {
        return new JsonResponse(
            $provider->getOrder($id)
        );
    }
}
