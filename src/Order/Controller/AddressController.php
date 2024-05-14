<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Order\Service\FixedAddressProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AddressController extends AbstractController
{
    #[Route(path: '/address/list', name: 'query-all-addresses', methods: ['GET'])]
    public function getAllFixedAddress(FixedAddressProvider $provider): JsonResponse
    {
        return new JsonResponse(
            $provider->getAllFixedAddress()
        );
    }

    #[Route(path: '/address/{id<\d+>}', name: 'query-single-address', methods: ['GET'])]
    public function getFixedAddress(int $id, FixedAddressProvider $provider): JsonResponse
    {
        return new JsonResponse(
            $provider->getFixedAddress($id)
        );
    }

    #[Route(path: '/address/create', name: 'command-create-address', methods: ['POST'], format: 'json')]
    public function createFixedAddress(): JsonResponse
    {
        echo "tutaj " . basename(__FILE__) .' '. __LINE__ . "\n"; exit; // FIXME
        // return new JsonResponse(
        //     $provider->getFixedAddress($id)
        // );
    }

}
