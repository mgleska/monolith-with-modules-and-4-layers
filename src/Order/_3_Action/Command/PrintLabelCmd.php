<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Command;

use App\Order\_2_Export\Command\PrintLabelInterface;
use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use App\Printer\Export\Dto\AddressDto as PrintAddressDto;
use App\Printer\Export\Dto\GoodsLineDto as PrintGoodsLineDto;
use App\Printer\Export\Dto\PrintLabelDto;
use App\Printer\Export\Dto\SsccDto as PrintSsccDto;
use App\Printer\Export\PrintLabelInterface as PrintPrintLabel;
use Doctrine\DBAL\Exception as DBALException;
use Exception;

class PrintLabelCmd implements PrintLabelInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderHeaderRepository $orderHeaderRepository,
        private readonly PrintPrintLabel $printLabelCmd,
        private readonly OrderValidator $orderValidator,
    ) {
    }

    /**
     * @return array{bool, string}
     * @throws DBALException
     * @throws Exception
     */
    public function printLabel(int $orderId): array
    {
        $order = $this->orderRepository->get($orderId, true, true);
        $this->orderValidator->validateExists($order);
        $this->orderValidator->validateHasAccess($order);

        $label = $this->printLabelCmd->printLabel($this->prepareLabelData($order));
        $ok = $this->orderHeaderRepository->testAndChangeStatus($orderId, OrderStatusEnum::CONFIRMED, OrderStatusEnum::PRINTED);
        if ($ok) {
            return [true, $label];
        } else {
            return [false, 'ORDER_PRINT_LABEL_STATUS_NOT_VALID_FOR_PRINT'];
        }
    }

    private function prepareLabelData(Order $order): PrintLabelDto
    {
        $loadingAddress = new PrintAddressDto(
            substr($order->getHeader()->getLoadingNameCompanyOrPerson(), 0, 40),
            substr($order->getHeader()->getLoadingAddress(), 0, 40),
            substr($order->getHeader()->getLoadingZipCode(), 0, 15),
            substr($order->getHeader()->getLoadingCity(), 0, 25),
        );

        $deliveryAddress = new PrintAddressDto(
            substr($order->getHeader()->getDeliveryNameCompanyOrPerson(), 0, 40),
            substr($order->getHeader()->getDeliveryAddress(), 0, 40),
            substr($order->getHeader()->getDeliveryZipCode(), 0, 15),
            substr($order->getHeader()->getDeliveryCity(), 0, 25),
        );

        $lines = [];
        foreach ($order->getLines() as $line) {
            $lines[] = new PrintGoodsLineDto(
                substr($line->getGoodsDescription(), 0, 25),
                $line->getQuantity(),
            );
        }

        $ssccs = [];
        foreach ($order->getSsccs() as $item) {
            $ssccs[] = new PrintSsccDto(
                $item->getCode(),
            );
        }

        return new PrintLabelDto($loadingAddress, $deliveryAddress, $lines, $ssccs);
    }
}
