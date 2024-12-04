<?php

declare(strict_types=1);

namespace App\Customer\_3_Action\Helper;

use App\Customer\_2_Export\Dto\CustomerDto;
use App\Customer\_3_Action\Entity\Customer;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class CustomerHelper
{
    public static function createCustomerDtoFromEntity(Customer $entity): CustomerDto
    {
        return new CustomerDto(
            $entity->getId(),
            $entity->getVersion(),
            $entity->getName(),
            $entity->getDbNameSuffix()
        );
    }
}
