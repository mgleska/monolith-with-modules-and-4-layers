<?php

declare(strict_types=1);

namespace App\Printer\Service;

use App\Api\Export\ApiProblemException;
use App\Order\Export\GetOrderInterface;
use function sprintf;
use function str_repeat;

class PrintCommand
{
    public function __construct(
        private readonly GetOrderInterface $getOrder,
    )
    {}

    /**
     * @throws ApiProblemException
     */
    public function printLabel(int $orderId): string
    {
        $order = $this->getOrder->getOrder($orderId);

        $label = sprintf("%-40s | %-40s\n", 'Loading Address', 'Delivery Address');
        $label .= str_repeat('-', 81) . "\n";
        $label .= sprintf("%-40s | %-40s\n", $order->loadingAddress->nameCompanyOrPerson, $order->deliveryAddress->nameCompanyOrPerson);
        $label .= sprintf("%-40s | %-40s\n", $order->loadingAddress->address, $order->deliveryAddress->address);
        $label .= sprintf("%-15s %-25s | %-15s %-24s\n", $order->loadingAddress->zipCode, $order->loadingAddress->city,
            $order->deliveryAddress->zipCode, $order->deliveryAddress->city);
        $label .= str_repeat('-', 81) . "\n";

        $count = 1;
        foreach ($order->lines as $line) {
            $label .= sprintf("| %3d | %-25s | %2d\n", $count, $line->goodsDescription, $line->quantity);
            $count += 1;
        }
        $label .= str_repeat('-', 81) . "\n";

        $column = 1;
        foreach ($order->ssccs as $sscc) {
            if ($column > 1) {
                $label .= ' | ';
            }
            $label .= sprintf("%18s", $sscc->code);
            $column += 1;
            if ($column > 4) {
                $label .= "\n";
                $column = 1;
            }
        }

        return $label;
    }
}
