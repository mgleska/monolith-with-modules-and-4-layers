<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Api\Export\Dto\FailResponseDto;
use App\Api\Export\Dto\SuccessResponseDto;
use App\Order\Export\Dto\Order\SendOrderDto;
use App\Order\Service\OrderCommand;
use App\Order\Service\OrderQuery;
use Doctrine\DBAL\Exception as DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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

    /**
     * @throws DBALException
     */
    #[Route(path: '/order/send', name: 'command-send-order', methods: ['POST'], format: 'json')]
    public function sendOrder(
        #[MapRequestPayload] SendOrderDto $dto,
        OrderCommand $service,
    ): JsonResponse
    {
        [$ok, $message] = $service->sendOrder($dto->id);
        if ($ok) {
            return new JsonResponse(new SuccessResponseDto(), Response::HTTP_OK);
        }
        else {
            return new JsonResponse(new FailResponseDto($message), Response::HTTP_BAD_REQUEST);
        }
    }
}
