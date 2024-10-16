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
use App\Order\_3_Action\Entity\OrderHeader;
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_3_Action\Validator\FixedAddressValidator;
use App\Order\_3_Action\Validator\GenericDtoValidator;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use App\Tests\AutoincrementIdTrait;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RepositoryMock\RepositoryMockTrait;

class CreateOrderCmdTest extends TestCase
{
    use RepositoryMockTrait;
    use AutoincrementIdTrait;

    private CreateOrderCmd $sut;

    private MockObject|OrderRepository $orderRepository;
    private MockObject|OrderHeaderRepository $orderHeaderRepository;
    private MockObject|OrderValidator $validator;
    private MockObject|LoggerInterface $logger;
    private MockObject|UserBagInterface $userBag;
    private MockObject|FixedAddressRepository $addressRepository;

    private const CUSTOMER_ID = 11;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->orderHeaderRepository = $this->createMock(OrderHeaderRepository::class);
        $this->validator = $this->createMock(OrderValidator::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userBag = $this->createMock(UserBagInterface::class);
        $this->addressRepository = $this->createMock(FixedAddressRepository::class);

        $this->userBag->method('getCustomerId')->willReturn(self::CUSTOMER_ID);
        self::setAutoincrementIdForClass(OrderHeader::class, 5);

        $this->sut = new CreateOrderCmd(
            $this->orderRepository,
            $this->orderHeaderRepository,
            $this->validator,
            $this->logger,
            $this->userBag,
            $this->createMock(GenericDtoValidator::class),
            $this->addressRepository,
            $this->createMock(FixedAddressValidator::class)
        );
    }

    /**
     * @param array<string, mixed> $dtoData
     * @param array<string, mixed> $expectedHeader
     * @param array<int, array<string, mixed>> $expectedLines
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    #[Test]
    #[DataProvider('dataProviderCreateOrder')]
    public function createOrder(
        array $dtoData,
        ?FixedAddress $fixedAddress,
        int $expectedId,
        array $expectedHeader,
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

        $this->logger->expects(self::once())->method('info');

        $this->addressRepository->method('findOneBy')->willReturn($fixedAddress);

        $storedOrder = null;
        $this->orderRepository->method('storeNew')->willReturnCallback(
            function (Order $order) use (&$storedOrder) {
                self::setAutoincrementId($order->getHeader());
                foreach ($order->getLines() as $line) {
                    self::setAutoincrementId($line);
                }
                $storedOrder = unserialize(serialize($order));
            }
        );

        $result = $this->sut->createOrder($dto);

        $this->assertSame($expectedId, $result);

        $storedOrderHader = $storedOrder->getHeader();
        $this->assertSame($expectedId, $storedOrder->getId());
        $this->assertSame($expectedHeader['customerId'], $storedOrderHader->getCustomerId());
        $this->assertMatchesRegularExpression($expectedHeader['number'], $storedOrderHader->getNumber());
        $this->assertSame($expectedHeader['status'], $storedOrderHader->getStatus());
        $this->assertSame($expectedHeader['quantityTotal'], $storedOrderHader->getQuantityTotal());
        $this->assertEquals(new DateTime($expectedHeader['loadingDate']), $storedOrderHader->getLoadingDate());
        $this->assertSame($expectedHeader['loadingNameCompanyOrPerson'], $storedOrderHader->getLoadingNameCompanyOrPerson());
        $this->assertSame($expectedHeader['loadingAddress'], $storedOrderHader->getLoadingAddress());
        $this->assertSame($expectedHeader['loadingCity'], $storedOrderHader->getLoadingCity());
        $this->assertSame($expectedHeader['loadingZipCode'], $storedOrderHader->getLoadingZipCode());
        $this->assertSame($expectedHeader['loadingContactPerson'], $storedOrderHader->getLoadingContactPerson());
        $this->assertSame($expectedHeader['loadingContactPhone'], $storedOrderHader->getLoadingContactPhone());
        $this->assertSame($expectedHeader['loadingContactEmail'], $storedOrderHader->getLoadingContactEmail());
        $this->assertSame($expectedHeader['loadingFixedAddressExternalId'], $storedOrderHader->getLoadingFixedAddressExternalId());
        $this->assertSame($expectedHeader['deliveryNameCompanyOrPerson'], $storedOrderHader->getDeliveryNameCompanyOrPerson());
        $this->assertSame($expectedHeader['deliveryAddress'], $storedOrderHader->getDeliveryAddress());
        $this->assertSame($expectedHeader['deliveryCity'], $storedOrderHader->getDeliveryCity());
        $this->assertSame($expectedHeader['deliveryZipCode'], $storedOrderHader->getDeliveryZipCode());
        $this->assertSame($expectedHeader['deliveryContactPerson'], $storedOrderHader->getDeliveryContactPerson());
        $this->assertSame($expectedHeader['deliveryContactPhone'], $storedOrderHader->getDeliveryContactPhone());
        $this->assertSame($expectedHeader['deliveryContactEmail'], $storedOrderHader->getDeliveryContactEmail());

        $this->assertSame(count($expectedLines), count($storedOrder->getLines()));
        foreach ($storedOrder->getLines() as $orderLine) {
            $this->assertSame(self::CUSTOMER_ID, $orderLine->getCustomerId());
            $this->assertSame($expectedId, $orderLine->getOrderHeader()->getId());
            $this->assertTrue(
                $this->isMatchingOrderLine($orderLine, $expectedLines),
                sprintf(
                    'OrderLine with id="%d" description="%s" not match expected.',
                    $orderLine->getId(),
                    $orderLine->getGoodsDescription()
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
                'fixedAddress' => null,
                'expectedId' => 5,
                'expectedHeader' => [
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
                'fixedAddress' => self::createFakeObject(FixedAddress::class, [
                    'id' => 20,
                    'customerId' => self::CUSTOMER_ID,
                    'externalId' => 'WH1',
                    'nameCompanyOrPerson' => 'Warehouse 1',
                    'address' => 'Dworcowa 2',
                    'city' => 'Poznań',
                    'zipCode' => '61-801'
                ]),
                'expectedId' => 5,
                'expectedHeader' => [
                    'customerId' => self::CUSTOMER_ID,
                    'number' => '#^[0-9]+/[0-9]{8}/[0-9]+$#',
                    'status' => OrderStatusEnum::NEW,
                    'quantityTotal' => 8,
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
}
