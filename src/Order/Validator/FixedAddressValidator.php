<?php

declare(strict_types=1);

namespace App\Order\Validator;

use App\Order\Entity\FixedAddress;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FixedAddressValidator
{
    public function validateExists(?FixedAddress $address): void
    {
        if ($address === null) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'ORDER_FIXEDADDRESS_NOT_FOUND');
        }
    }
}
