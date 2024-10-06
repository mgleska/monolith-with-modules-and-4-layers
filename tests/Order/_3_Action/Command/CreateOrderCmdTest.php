<?php

declare(strict_types=1);

namespace App\Tests\Order\_3_Action\Command;

use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\CreateOrderDto;
use App\Order\_2_Export\Dto\Order\OrderAddressContactDto;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_2_Export\Dto\Order\OrderLineDto;
use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Command\CreateOrderCmd;
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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RepositoryMock\RepositoryMockObject;
use RepositoryMock\RepositoryMockTrait;

use function count;

class CreateOrderCmdTest extends TestCase
{
    use RepositoryMockTrait;

    private CreateOrderCmd $sut;

    private RepositoryMockObject|OrderRepository $orderRepository;
    private MockObject|OrderValidator $validator;
    private MockObject|LoggerInterface $logger;
    private MockObject|UserBagInterface $userBag;
    private MockObject|EntityManagerInterface $entityManager;
    private RepositoryMockObject|OrderLineRepository $orderLineRepository;
    private MockObject|GenericDtoValidator $dtoValidator;

    private const CUSTOMER_ID = 11;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->createRepositoryMock(OrderRepository::class);
        $this->validator = $this->createMock(OrderValidator::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userBag = $this->createMock(UserBagInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->orderLineRepository = $this->createRepositoryMock(OrderLineRepository::class);
        $this->dtoValidator = $this->createMock(GenericDtoValidator::class);

        $this->userBag->method('getCustomerId')->willReturn(self::CUSTOMER_ID);

        $this->sut = new CreateOrderCmd(
            $this->orderRepository,
            $this->validator,
            $this->logger,
            $this->userBag,
            $this->entityManager,
            $this->orderLineRepository,
            $this->dtoValidator,
        );
    }

    /**
     * @param array<string, mixed> $dtoData
     * @param array<string, mixed> $expectedOrder
     * @param array<int, array<string, mixed>> $expectedLines
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    #[Test]
    #[DataProvider('dataProviderCreateOrder')]
    public function createOrder(
        array $dtoData,
        ?FixedAddress $validatorResponse,
        int $expectedId,
        array $expectedOrder,
        array $expectedLines
    ): void {
        $dtoData['loadingAddress'] = $dtoData['loadingAddress'] ? new OrderAddressDto(...$dtoData['loadingAddress']) : null;
        $dtoData['loadingContact'] = new OrderAddressContactDto(...$dtoData['loadingContact']);
        $dtoData['deliveryAddress'] = new OrderAddressDto(...$dtoData['deliveryAddress']);
        $dtoData['deliveryContact'] = new OrderAddressContactDto(...$dtoData['deliveryContact']);
        $lines = [];
        foreach ($dtoData['lines'] as $line) {
            $lines[] = new OrderLineDto(...$line);
        }
        $dtoData['lines'] = $lines;
        $dto = new CreateOrderDto(...$dtoData);

        $this->entityManager->expects(self::once())->method('commit');
        $this->logger->expects(self::once())->method('info');

        $this->orderRepository->loadStore([['id' => 4]]);

        $this->validator->method('validateLoadingAddressForCreate')->willReturn($validatorResponse);

        $result = $this->sut->createOrder($dto);

        $this->assertSame($expectedId, $result);

        $dbOrder = $this->orderRepository->getStoreContent();
        /** @var Order $order */
        $order = $dbOrder[$expectedId];
        $this->assertSame($expectedId, $order->getId());
        $this->assertSame($expectedOrder['customerId'], $order->getCustomerId());
        $this->assertMatchesRegularExpression($expectedOrder['number'], $order->getNumber());
        $this->assertSame($expectedOrder['status'], $order->getStatus());
        $this->assertSame($expectedOrder['quantityTotal'], $order->getQuantityTotal());
        $this->assertEquals(new DateTime($expectedOrder['loadingDate']), $order->getLoadingDate());
        $this->assertSame($expectedOrder['loadingNameCompanyOrPerson'], $order->getLoadingNameCompanyOrPerson());
        $this->assertSame($expectedOrder['loadingAddress'], $order->getLoadingAddress());
        $this->assertSame($expectedOrder['loadingCity'], $order->getLoadingCity());
        $this->assertSame($expectedOrder['loadingZipCode'], $order->getLoadingZipCode());
        $this->assertSame($expectedOrder['loadingContactPerson'], $order->getLoadingContactPerson());
        $this->assertSame($expectedOrder['loadingContactPhone'], $order->getLoadingContactPhone());
        $this->assertSame($expectedOrder['loadingContactEmail'], $order->getLoadingContactEmail());
        $this->assertSame($expectedOrder['loadingFixedAddressExternalId'], $order->getLoadingFixedAddressExternalId());
        $this->assertSame($expectedOrder['deliveryNameCompanyOrPerson'], $order->getDeliveryNameCompanyOrPerson());
        $this->assertSame($expectedOrder['deliveryAddress'], $order->getDeliveryAddress());
        $this->assertSame($expectedOrder['deliveryCity'], $order->getDeliveryCity());
        $this->assertSame($expectedOrder['deliveryZipCode'], $order->getDeliveryZipCode());
        $this->assertSame($expectedOrder['deliveryContactPerson'], $order->getDeliveryContactPerson());
        $this->assertSame($expectedOrder['deliveryContactPhone'], $order->getDeliveryContactPhone());
        $this->assertSame($expectedOrder['deliveryContactEmail'], $order->getDeliveryContactEmail());

        $dbOrderLine = $this->orderLineRepository->getStoreContent();
        $this->assertSame(count($expectedLines), count($dbOrderLine));
        /** @var OrderLine $orderLineEntity */
        foreach ($dbOrderLine as $orderLineEntity) {
            $this->assertSame(self::CUSTOMER_ID, $orderLineEntity->getCustomerId());
            $this->assertSame($expectedId, $orderLineEntity->getOrder()->getId());
            $this->assertTrue(
                $this->isMatchingOrderLine($orderLineEntity, $expectedLines),
                sprintf(
                    'OrderLine with id=%d and description="%s" found in database mock is not expected.',
                    $orderLineEntity->getId(),
                    $orderLineEntity->getGoodsDescription()
                )
            );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function dataProviderCreateOrder(): array
    {
        return [
            'explicit-loading-address-no-lines' => [
                'dtoData' => [
                    'loadingDate' => '2024-06-03',
                    'loadingFixedAddressExternalId' => null,
                    'loadingAddress' => [
                        'nameCompanyOrPerson' => 'Load Place',
                        'address' => 'ul. Garbary 125',
                        'city' => 'Poznań',
                        'zipCode' => '61-719'
                    ],
                    'loadingContact' => [
                        'contactPerson' => 'Contact Person',
                        'contactPhone' => '+48-222-222-222',
                        'contactEmail' => 'person@email.com',
                    ],
                    'deliveryAddress' => [
                        'nameCompanyOrPerson' => 'Delivery Place',
                        'address' => 'ul. Wschodnia',
                        'city' => 'Poznań',
                        'zipCode' => '61-001'
                    ],
                    'deliveryContact' => [
                        'contactPerson' => 'Person2',
                        'contactPhone' => '+48-111-111-111',
                        'contactEmail' => null,
                    ],
                    'lines' => [],
                ],
                'validatorResponse' => null,
                'expectedId' => 5,
                'expectedOrder' => [
                    'customerId' => self::CUSTOMER_ID,
                    'number' => '#^[0-9]+/[0-9]{8}/[0-9]+$#',
                    'status' => OrderStatusEnum::NEW,
                    'quantityTotal' => 0,
                    'loadingDate' => '2024-06-03',
                    'loadingNameCompanyOrPerson' => 'Load Place',
                    'loadingAddress' => 'ul. Garbary 125',
                    'loadingCity' => 'Poznań',
                    'loadingZipCode' => '61-719',
                    'loadingContactPerson' => 'Contact Person',
                    'loadingContactPhone' => '+48-222-222-222',
                    'loadingContactEmail' => 'person@email.com',
                    'loadingFixedAddressExternalId' => null,
                    'deliveryNameCompanyOrPerson' => 'Delivery Place',
                    'deliveryAddress' => 'ul. Wschodnia',
                    'deliveryCity' => 'Poznań',
                    'deliveryZipCode' => '61-001',
                    'deliveryContactPerson' => 'Person2',
                    'deliveryContactPhone' => '+48-111-111-111',
                    'deliveryContactEmail' => null,
                ],
                'expectedLines' => [],
            ],
            'fixed-loading-address-and-two-lines' => [
                'dtoData' => [
                    'loadingDate' => '2024-06-03',
                    'loadingFixedAddressExternalId' => 'WH1',
                    'loadingAddress' => null,
                    'loadingContact' => [
                        'contactPerson' => 'Contact Person',
                        'contactPhone' => '+48-222-222-222',
                        'contactEmail' => 'person@email.com',
                    ],
                    'deliveryAddress' => [
                        'nameCompanyOrPerson' => 'Delivery Place',
                        'address' => 'ul. Wschodnia',
                        'city' => 'Poznań',
                        'zipCode' => '61-001'
                    ],
                    'deliveryContact' => [
                        'contactPerson' => 'Person2',
                        'contactPhone' => '+48-111-111-111',
                        'contactEmail' => null,
                    ],
                    'lines' => [
                        [
                            'id' => null,
                            'quantity' => 3,
                            'length' => 120,
                            'width' => 80,
                            'height' => 100,
                            'weightOnePallet' => 200,
                            'weightTotal' => null,
                            'goodsDescription' => 'computers',
                        ],
                        [
                            'id' => null,
                            'quantity' => 5,
                            'length' => 120,
                            'width' => 80,
                            'height' => 100,
                            'weightOnePallet' => 200,
                            'weightTotal' => null,
                            'goodsDescription' => 'printers',
                        ],
                    ],
                ],
                'validatorResponse' => self::createFakeObject(FixedAddress::class, [
                    'id' => 20,
                    'customerId' => self::CUSTOMER_ID,
                    'externalId' => 'WH1',
                    'nameCompanyOrPerson' => 'Warehouse 1',
                    'address' => 'Dworcowa 2',
                    'city' => 'Poznań',
                    'zipCode' => '61-801'
                ]),
                'expectedId' => 5,
                'expectedOrder' => [
                    'customerId' => self::CUSTOMER_ID,
                    'number' => '#^[0-9]+/[0-9]{8}/[0-9]+$#',
                    'status' => OrderStatusEnum::NEW,
                    'quantityTotal' => 0,
                    'loadingDate' => '2024-06-03',
                    'loadingNameCompanyOrPerson' => 'Warehouse 1',
                    'loadingAddress' => 'Dworcowa 2',
                    'loadingCity' => 'Poznań',
                    'loadingZipCode' => '61-801',
                    'loadingContactPerson' => 'Contact Person',
                    'loadingContactPhone' => '+48-222-222-222',
                    'loadingContactEmail' => 'person@email.com',
                    'loadingFixedAddressExternalId' => 'WH1',
                    'deliveryNameCompanyOrPerson' => 'Delivery Place',
                    'deliveryAddress' => 'ul. Wschodnia',
                    'deliveryCity' => 'Poznań',
                    'deliveryZipCode' => '61-001',
                    'deliveryContactPerson' => 'Person2',
                    'deliveryContactPhone' => '+48-111-111-111',
                    'deliveryContactEmail' => null,
                ],
                'expectedLines' => [
                    [
                        'quantity' => 3,
                        'length' => 120,
                        'width' => 80,
                        'height' => 100,
                        'weightOnePallet' => 20000,
                        'weightTotal' => 60000,
                        'goodsDescription' => 'computers',
                    ],
                    [
                        'quantity' => 5,
                        'length' => 120,
                        'width' => 80,
                        'height' => 100,
                        'weightOnePallet' => 20000,
                        'weightTotal' => 100000,
                        'goodsDescription' => 'printers',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $expectedLines
     */
    private function isMatchingOrderLine(OrderLine $entity, array $expectedLines): bool
    {
        $matchFound = false;
        foreach ($expectedLines as $line) {
            if (
                $line['quantity'] !== $entity->getQuantity()
                || $line['length'] !== $entity->getLength()
                || $line['width'] !== $entity->getWidth()
                || $line['height'] !== $entity->getHeight()
                || $line['weightOnePallet'] !== $entity->getWeightOnePallet()
                || $line['weightTotal'] !== $entity->getWeightTotal()
                || $line['goodsDescription'] !== $entity->getGoodsDescription()
            ) {
                continue;
            }
            $matchFound = true;
            break;
        }

        return $matchFound;
    }

    /**
     * @param array<string, mixed> $dtoData
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    #[Test]
    #[DataProvider('dataProviderCreateOrderWithRollback')]
    public function createOrderWithRollback(
        array $dtoData,
    ): void {
        $dtoData['loadingAddress'] = $dtoData['loadingAddress'] ? new OrderAddressDto(...$dtoData['loadingAddress']) : null;
        $dtoData['loadingContact'] = new OrderAddressContactDto(...$dtoData['loadingContact']);
        $dtoData['deliveryAddress'] = new OrderAddressDto(...$dtoData['deliveryAddress']);
        $dtoData['deliveryContact'] = new OrderAddressContactDto(...$dtoData['deliveryContact']);
        $dto = new CreateOrderDto(...$dtoData);

        $this->expectException(Exception::class);

        $this->entityManager->method('flush')->willThrowException(new Exception('Simulated DB failure'));

        $this->entityManager->expects(self::once())->method('rollback');
        $this->logger->expects(self::never())->method('info');

        $this->sut->createOrder($dto);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderCreateOrderWithRollback(): array
    {
        return [
            'explicit-loading-address-no-lines' => [
                'dtoData' => [
                    'loadingDate' => '2024-06-03',
                    'loadingFixedAddressExternalId' => null,
                    'loadingAddress' => [
                        'nameCompanyOrPerson' => 'Load Place',
                        'address' => 'ul. Garbary 125',
                        'city' => 'Poznań',
                        'zipCode' => '61-719'
                    ],
                    'loadingContact' => [
                        'contactPerson' => 'Contact Person',
                        'contactPhone' => '+48-222-222-222',
                        'contactEmail' => 'person@email.com',
                    ],
                    'deliveryAddress' => [
                        'nameCompanyOrPerson' => 'Delivery Place',
                        'address' => 'ul. Wschodnia',
                        'city' => 'Poznań',
                        'zipCode' => '61-001'
                    ],
                    'deliveryContact' => [
                        'contactPerson' => 'Person2',
                        'contactPhone' => '+48-111-111-111',
                        'contactEmail' => null,
                    ],
                    'lines' => [],
                ],
            ],
        ];
    }
}
