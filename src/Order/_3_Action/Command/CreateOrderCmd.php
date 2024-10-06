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
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_3_Action\Validator\GenericDtoValidator;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderLineRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class CreateOrderCmd implements CreateOrderInterface
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderValidator $orderValidator,
        private readonly LoggerInterface $logger,
        private readonly UserBagInterface $userBag,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderLineRepository $orderLineRepository,
        private readonly GenericDtoValidator $dtoValidator,
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

        $fixedAddress = $this->orderValidator->validateLoadingAddressForCreate($dto->loadingFixedAddressExternalId, $dto->loadingAddress);

        $order = $this->createOrderEntity($dto, $fixedAddress);
        try {
            $this->entityManager->beginTransaction();
            $this->orderRepository->save($order, true);

            $quantity = 0;
            foreach ($dto->lines as $lineDto) {
                $line = $this->createOrderLineEntity($lineDto, $order);
                $quantity += $line->getQuantity();
                $this->orderLineRepository->save($line);
            }

            $order->setQuantityTotal($quantity);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        $this->logger->info('Created order with id {id} and number {nr}.', ['id' => $order->getId(), 'nr' => $order->getNumber()]);

        return $order->getId();
    }

    /**
     * @throws Exception
     */
    private function createOrderEntity(CreateOrderDto $dto, FixedAddress|null $fixedAddress): Order
    {
        $order = new Order();
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
            $count = $this->orderRepository->count(['customerId' => $this->userBag->getCustomerId(), 'number' => $nr]);
        } while ($count > 0);

        return $nr;
    }

    private function createOrderLineEntity(OrderLineDto $lineDto, Order $order): OrderLine
    {
        $entity = new OrderLine();
        $entity->setCustomerId($this->userBag->getCustomerId());
        $entity->setOrder($order);
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
