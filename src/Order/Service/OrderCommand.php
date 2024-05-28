<?php

declare(strict_types=1);

namespace App\Order\Service;

use App\Order\Enum\OrderStatusEnum;
use App\Order\Export\Dto\Order\OrderDto;
use App\Order\Repository\OrderRepository;
use App\Order\Validator\OrderValidator;
use App\Printer\Export\Dto\AddressDto as PrintAddressDto;
use App\Printer\Export\Dto\GoodsLineDto as PrintGoodsLineDto;
use App\Printer\Export\Dto\PrintLabelDto;
use App\Printer\Export\Dto\SsccDto as PrintSsccDto;
use App\Printer\Export\PrintLabelInterface;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface;

class OrderCommand
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderValidator $validator,
        private readonly LoggerInterface $logger,
        private readonly PrintLabelInterface $printLabel,
        private readonly OrderQuery $orderQuery,
    ) {
    }

    /**
     * @return array{bool, string}
     * @throws DBALException
     */
    public function sendOrder(int $orderId): array
    {
        $this->validator->validateHasAccess($orderId);

        $ok = $this->orderRepository->changeStatus($orderId, OrderStatusEnum::NEW, OrderStatusEnum::SENT);
        if ($ok) {
            $this->logger->info('Order with id {id} sent.', ['id' => $orderId]);
            return [true, ''];
        } else {
            return [false, 'ORDER_STATUS_NOT_VALID_FOR_SEND'];
        }
    }

    /**
     * @return array{bool, string}
     * @throws DBALException
     */
    public function printLabel(int $orderId): array
    {
        $orderDto = $this->orderQuery->getOrder($orderId);

        if ($orderDto->status !== OrderStatusEnum::CONFIRMED) {
            return [false, 'ORDER_PRINT_LABEL_STATUS_NOT_VALID_FOR_PRINT'];
        }

        $label = $this->printLabel->printLabel($this->prepareLabelData($orderDto), true);
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
                $line->goodsDescription,
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
