<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Api\Export\Dto\ApiProblemResponseDto;
use App\Api\Export\Dto\FailResponseDto;
use App\Api\Export\Dto\SuccessResponseDto;
use App\Order\Command\CreateOrderCmd;
use App\Order\Command\PrintLabelCmd;
use App\Order\Command\SendOrderCmd;
use App\Order\Export\Dto\Order\CreateOrderDto;
use App\Order\Export\Dto\Order\OrderDto;
use App\Order\Export\Dto\Order\PrintLabelDto;
use App\Order\Export\Dto\Order\SendOrderDto;
use App\Order\Query\OrderQuery;
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
        SendOrderCmd $service,
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
        PrintLabelCmd $service,
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
        CreateOrderCmd $service,
    ): Response {
        $id = $service->createOrder($dto);
        return new JsonResponse(new SuccessResponseDto(['id' => $id]), Response::HTTP_CREATED);
    }
}
