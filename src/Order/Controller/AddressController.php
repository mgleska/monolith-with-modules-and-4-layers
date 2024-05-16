<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Order\Dto\FixedAddressCreateDto;
use App\Order\Service\FixedAddressCommand;
use App\Order\Service\FixedAddressQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class AddressController extends AbstractController
{
    #[Route(path: '/address/list', name: 'query-all-addresses', methods: ['GET'], format: 'json')]
    public function getAllFixedAddress(FixedAddressQuery $provider): JsonResponse
    {
        return new JsonResponse(
            $provider->getAllFixedAddress()
        );
    }

    #[Route(path: '/address/{id<\d+>}', name: 'query-single-address', methods: ['GET'], format: 'json')]
    public function getFixedAddress(int $id, FixedAddressQuery $provider): JsonResponse
    {
        return new JsonResponse(
            $provider->getFixedAddress($id)
        );
    }

    #[Route(path: '/address/create', name: 'command-create-address', methods: ['POST'], format: 'json')]
    public function createFixedAddress(
        #[MapRequestPayload] FixedAddressCreateDto $dto,
        FixedAddressCommand $service,
    ): JsonResponse
    {
        $id = $service->createFixedAddress($dto);
        return new JsonResponse(['id' => $id], Response::HTTP_CREATED);
    }
}
