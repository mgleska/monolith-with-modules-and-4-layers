<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Command;

use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Command\CreateOrderInterface;
use App\Order\_2_Export\Dto\Order\CreateOrderDto;
use App\Order\_2_Export\Dto\Order\OrderLineDto;
use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderHeader;
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_3_Action\Validator\FixedAddressValidator;
use App\Order\_3_Action\Validator\GenericDtoValidator;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class CreateOrderCmd implements CreateOrderInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderHeaderRepository $orderHeaderRepository,
        private readonly OrderValidator $orderValidator,
        private readonly LoggerInterface $logger,
        private readonly UserBagInterface $userBag,
        private readonly GenericDtoValidator $dtoValidator,
        private readonly FixedAddressRepository $addressRepository,
        private readonly FixedAddressValidator $addressValidator,
    ) {
    }

    /**
     * @throws ValidationFailedException
     * @throws Exception
     */
    public function createOrder(CreateOrderDto $dto, bool $isValidated = false): int
    {
        if (! $isValidated) {
            $this->dtoValidator->validate($dto, 'createOrder');
        }

        if ($dto->loadingFixedAddressExternalId !== null) {
            $fixedAddress = $this->addressRepository->findOneBy(
                ['customerId' => $this->userBag->getCustomerId(), 'externalId' => $dto->loadingFixedAddressExternalId]
            );
            $this->addressValidator->validateExists($fixedAddress);
        } else {
            $fixedAddress = null;
        }

        $this->orderValidator->validateLoadingAddressForCreate($fixedAddress, $dto->loadingAddress);

        $orderHeader = $this->createOrderHeader($dto, $fixedAddress);

        $lines = [];
        $quantity = 0;
        foreach ($dto->lines as $lineDto) {
            $line = $this->createOrderLine($lineDto, $orderHeader);
            $lines[] = $line;
            $quantity += $line->getQuantity();
        }
        $orderHeader->setQuantityTotal($quantity);

        $order = new Order($orderHeader, $lines);
        $this->orderRepository->storeNew($order);

        $this->logger->info('Created order with id {id} and number {nr}.', ['id' => $order->getId(), 'nr' => $order->getHeader()->getNumber()]);

        return $order->getId();
    }

    /**
     * @throws Exception
     */
    private function createOrderHeader(CreateOrderDto $dto, FixedAddress|null $fixedAddress): OrderHeader
    {
        $order = new OrderHeader();
        $order->setCustomerId($this->userBag->getCustomerId());
        $order->setNumber($this->orderNumberGenerator());
        $order->setStatus(OrderStatusEnum::NEW);
        $order->setQuantityTotal(0);
        $order->setLoadingDate(new DateTime($dto->loadingDate));
        if ($fixedAddress !== null) {
            $order->setLoadingFixedAddressExternalId($dto->loadingFixedAddressExternalId);
            $order->setLoadingNameCompanyOrPerson($fixedAddress->getNameCompanyOrPerson());
            $order->setLoadingAddress($fixedAddress->getAddress());
            $order->setLoadingCity($fixedAddress->getCity());
            $order->setLoadingZipCode($fixedAddress->getZipCode());
        } else {
            $order->setLoadingFixedAddressExternalId(null);
            $order->setLoadingNameCompanyOrPerson($dto->loadingAddress->nameCompanyOrPerson);
            $order->setLoadingAddress($dto->loadingAddress->address);
            $order->setLoadingCity($dto->loadingAddress->city);
            $order->setLoadingZipCode($dto->loadingAddress->zipCode);
        }
        $order->setLoadingContactPerson($dto->loadingContact->contactPerson);
        $order->setLoadingContactPhone($dto->loadingContact->contactPhone);
        $order->setLoadingContactEmail($dto->loadingContact->contactEmail);
        $order->setDeliveryNameCompanyOrPerson($dto->deliveryAddress->nameCompanyOrPerson);
        $order->setDeliveryAddress($dto->deliveryAddress->address);
        $order->setDeliveryCity($dto->deliveryAddress->city);
        $order->setDeliveryZipCode($dto->deliveryAddress->zipCode);
        $order->setDeliveryContactPerson($dto->deliveryContact->contactPerson);
        $order->setDeliveryContactPhone($dto->deliveryContact->contactPhone);
        $order->setDeliveryContactEmail($dto->deliveryContact->contactEmail);

        return $order;
    }

    private function orderNumberGenerator(): string
    {
        do {
            $nr = $this->userBag->getCustomerId() . '/' . date('Ymd') . '/' . rand(1, 9999);
            $count = $this->orderHeaderRepository->count(['customerId' => $this->userBag->getCustomerId(), 'number' => $nr]);
        } while ($count > 0);

        return $nr;
    }

    private function createOrderLine(OrderLineDto $lineDto, OrderHeader $orderHeader): OrderLine
    {
        $entity = new OrderLine();
        $entity->setCustomerId($this->userBag->getCustomerId());
        $entity->setOrderHeader($orderHeader);
        $entity->setQuantity($lineDto->quantity);
        $entity->setLength($lineDto->length);
        $entity->setWidth($lineDto->width);
        $entity->setHeight($lineDto->height);
        $weight = (int)round($lineDto->weightOnePallet * 100);
        $entity->setWeightOnePallet($weight);
        $entity->setWeightTotal($lineDto->quantity * $weight);
        $entity->setGoodsDescription($lineDto->goodsDescription);

        return $entity;
    }
}
