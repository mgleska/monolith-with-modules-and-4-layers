<?php

declare(strict_types=1);

namespace App\Order\_1_Connector\Api;

use App\Api\_2_Export\Dto\ApiProblemResponseDto;
use App\Api\_2_Export\Dto\FailResponseDto;
use App\Api\_2_Export\Dto\SuccessResponseDto;
use App\Order\_2_Export\Command\CreateOrderInterface;
use App\Order\_2_Export\Command\PrintLabelInterface;
use App\Order\_2_Export\Command\SendOrderInterface;
use App\Order\_2_Export\Dto\Order\CreateOrderDto;
use App\Order\_2_Export\Dto\Order\OrderDto;
use App\Order\_2_Export\Dto\Order\PrintLabelDto;
use App\Order\_2_Export\Dto\Order\SendOrderDto;
use App\Order\_2_Export\Query\GetOrderInterface;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route(path: '/order/{id<\d+>}', name: 'query-single-order', methods: ['GET'], format: 'json')]
    #[OA\Response(response: 200, description: 'Returns order data.', content: new Model(type: OrderDto::class))]
    #[OA\Response(response: '400-499', description: 'some exception', content: new Model(type: ApiProblemResponseDto::class))]
    public function getOrder(int $id, GetOrderInterface $provider): JsonResponse
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
        SendOrderInterface $service,
    ): JsonResponse {
        [$ok, $message] = $service->sendOrder($dto->orderId);
        if ($ok) {
            return new JsonResponse(new SuccessResponseDto(), Response::HTTP_OK);
        } else {
            return new JsonResponse(new FailResponseDto($message), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws DBALException
     */
    #[Route(path: '/order/print-label', name: 'command-print-label', methods: ['POST'], format: 'json')]
    public function printLabel(
        #[MapRequestPayload] PrintLabelDto $dto,
        PrintLabelInterface $service,
    ): Response {
        [$ok, $response] = $service->printLabel($dto->orderId);
        if ($ok) {
            return new Response($response, 200, ['Content-Type' => 'text/plain']);
        } else {
            return new JsonResponse(new FailResponseDto($response), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/order/create', name: 'command-create-order', methods: ['POST'], format: 'json')]
    public function createOrder(
        #[MapRequestPayload] CreateOrderDto $dto,
        CreateOrderInterface $service,
    ): Response {
        $id = $service->createOrder($dto);
        return new JsonResponse(new SuccessResponseDto(['id' => $id]), Response::HTTP_CREATED);
    }
}
