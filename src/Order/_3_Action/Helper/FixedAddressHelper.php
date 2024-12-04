<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Helper;

use App\Order\_2_Export\Dto\FixedAddress\FixedAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;

class FixedAddressHelper
{
    public static function createFixedAddressDtoFromEntity(FixedAddress $entity): FixedAddressDto
    {
        return new FixedAddressDto(
            $entity->getId(),
            $entity->getVersion(),
            $entity->getExternalId(),
            $entity->getNameCompanyOrPerson(),
            $entity->getAddress(),
            $entity->getCity(),
            $entity->getZipCode()
        );
    }
}
