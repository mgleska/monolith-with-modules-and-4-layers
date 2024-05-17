<?php

declare(strict_types=1);

namespace App\Order\Export;

use App\Api\Export\ApiProblemException;
use App\Order\Export\Dto\Order\OrderDto;

interface GetOrderInterface
{
    /**
     * @throws ApiProblemException
     */
    public function getOrder(int $id): OrderDto;
}
