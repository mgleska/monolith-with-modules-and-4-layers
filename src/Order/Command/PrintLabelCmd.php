<?php

declare(strict_types=1);

namespace App\Order\Command;

use App\Order\Enum\OrderStatusEnum;
use App\Order\Export\Dto\Order\OrderDto;
use App\Order\Query\OrderQuery;
use App\Order\Repository\OrderRepository;
use App\Printer\Export\Dto\AddressDto as PrintAddressDto;
use App\Printer\Export\Dto\GoodsLineDto as PrintGoodsLineDto;
use App\Printer\Export\Dto\PrintLabelDto;
use App\Printer\Export\Dto\SsccDto as PrintSsccDto;
use App\Printer\Export\PrintLabelCmdInterface;
use Doctrine\DBAL\Exception as DBALException;
use Exception;

class PrintLabelCmd
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly PrintLabelCmdInterface $printLabelCmd,
        private readonly OrderQuery $orderQuery,
    ) {
    }

    /**
     * @return array{bool, string}
     * @throws DBALException
     * @throws Exception
     */
    public function printLabel(int $orderId): array
    {
        $orderDto = $this->orderQuery->getOrder($orderId);

        if ($orderDto->status !== OrderStatusEnum::CONFIRMED) {
            return [false, 'ORDER_PRINT_LABEL_STATUS_NOT_VALID_FOR_PRINT'];
        }

        $label = $this->printLabelCmd->printLabelCmd($this->prepareLabelData($orderDto));
        $this->orderRepository->changeStatus($orderId, OrderStatusEnum::CONFIRMED, OrderStatusEnum::PRINTED);

        return [true, $label];
    }

    private function prepareLabelData(Orderdto $orderDto): PrintLabelDto
    {
        $loadingAddress = new PrintAddressDto(
            substr($orderDto->loadingAddress->nameCompanyOrPerson, 0, 40),
            substr($orderDto->loadingAddress->address, 0, 40),
            substr($orderDto->loadingAddress->zipCode, 0, 15),
            substr($orderDto->loadingAddress->city, 0, 25),
        );

        $deliveryAddress = new PrintAddressDto(
            substr($orderDto->deliveryAddress->nameCompanyOrPerson, 0, 40),
            substr($orderDto->deliveryAddress->address, 0, 40),
            substr($orderDto->deliveryAddress->zipCode, 0, 15),
            substr($orderDto->deliveryAddress->city, 0, 25),
        );

        $lines = [];
        foreach ($orderDto->lines as $line) {
            $lines[] = new PrintGoodsLineDto(
                substr($line->goodsDescription, 0, 25),
                $line->quantity,
            );
        }

        $ssccs = [];
        foreach ($orderDto->ssccs as $item) {
            $ssccs[] = new PrintSsccDto(
                $item->code,
            );
        }

        return new PrintLabelDto($loadingAddress, $deliveryAddress, $lines, $ssccs);
    }
}
