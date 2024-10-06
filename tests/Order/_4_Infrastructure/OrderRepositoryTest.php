<?php

declare(strict_types=1);

namespace App\Tests\Order\_4_Infrastructure;

use App\Order\_2_Export\Enum\OrderStatusEnum;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderHeader;
use App\Order\_3_Action\Entity\OrderLine;
use App\Order\_4_Infrastructure\Repository\OrderLineRepository;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use App\Order\_4_Infrastructure\Repository\OrderSsccRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use RepositoryMock\RepositoryMockObject;
use RepositoryMock\RepositoryMockTrait;

class OrderRepositoryTest extends TestCase
{
    use RepositoryMockTrait;

    private OrderRepository $sut;

    private MockObject|EntityManagerInterface $entityManager;
    private RepositoryMockObject|OrderHeaderRepository $headerRepository;
    private RepositoryMockObject|OrderLineRepository $lineRepository;
    private RepositoryMockObject|OrderSsccRepository $ssccRepository;

    private const CUSTOMER_ID = 11;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->headerRepository = $this->createRepositoryMock(OrderHeaderRepository::class);
        $this->lineRepository = $this->createRepositoryMock(OrderLineRepository::class);
        $this->ssccRepository = $this->createRepositoryMock(OrderSsccRepository::class);

        $this->sut = new OrderRepository(
            $this->entityManager,
            $this->headerRepository,
            $this->lineRepository,
            $this->ssccRepository
        );
    }

    /**
     * @param array<string, mixed> $headerData
     * @param array<int, array<string, mixed>> $linesData
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @throws ReflectionException
     */
    #[Test]
    #[DataProvider('dataProviderStoreNew')]
    public function storeNew(
        int $expectedId,
        array $headerData,
        array $linesData
    ): void {
        $header = self::createFakeObject(OrderHeader::class, $headerData);
        $lines = [];
        foreach ($linesData as $row) {
            $line = self::createFakeObject(OrderLine::class, $row);
            $line->setOrderHeader($header);
            $lines[] = $line;
        }
        $order = new Order($header, $lines);

        $this->entityManager->expects(self::once())->method('commit');

        $this->headerRepository->loadStore([['id' => 4]]);

        $this->sut->storeNew($order);

        $this->assertSame($expectedId, $order->getId());

        $dbHeader = $this->headerRepository->getStoreContent();
        /** @var OrderHeader $header */
        $header = $dbHeader[$expectedId];
        $this->assertSame($expectedId, $header->getId());
        $this->assertSame($headerData['customerId'], $header->getCustomerId());
        $this->assertSame($headerData['number'], $header->getNumber());
        $this->assertSame($headerData['status'], $header->getStatus());
        $this->assertSame($headerData['quantityTotal'], $header->getQuantityTotal());
        $this->assertEquals($headerData['loadingDate'], $header->getLoadingDate());
        $this->assertSame($headerData['loadingNameCompanyOrPerson'], $header->getLoadingNameCompanyOrPerson());
        $this->assertSame($headerData['loadingAddress'], $header->getLoadingAddress());
        $this->assertSame($headerData['loadingCity'], $header->getLoadingCity());
        $this->assertSame($headerData['loadingZipCode'], $header->getLoadingZipCode());
        $this->assertSame($headerData['loadingContactPerson'], $header->getLoadingContactPerson());
        $this->assertSame($headerData['loadingContactPhone'], $header->getLoadingContactPhone());
        $this->assertSame($headerData['loadingContactEmail'], $header->getLoadingContactEmail());
        $this->assertSame($headerData['loadingFixedAddressExternalId'], $header->getLoadingFixedAddressExternalId());
        $this->assertSame($headerData['deliveryNameCompanyOrPerson'], $header->getDeliveryNameCompanyOrPerson());
        $this->assertSame($headerData['deliveryAddress'], $header->getDeliveryAddress());
        $this->assertSame($headerData['deliveryCity'], $header->getDeliveryCity());
        $this->assertSame($headerData['deliveryZipCode'], $header->getDeliveryZipCode());
        $this->assertSame($headerData['deliveryContactPerson'], $header->getDeliveryContactPerson());
        $this->assertSame($headerData['deliveryContactPhone'], $header->getDeliveryContactPhone());
        $this->assertSame($headerData['deliveryContactEmail'], $header->getDeliveryContactEmail());

        $dbOrderLines = $this->lineRepository->getStoreContent();
        $this->assertSame(count($lines), count($dbOrderLines));
        /** @var OrderLine $orderLine */
        foreach ($dbOrderLines as $orderLine) {
            $this->assertSame(self::CUSTOMER_ID, $orderLine->getCustomerId());
            $this->assertSame($expectedId, $orderLine->getOrderHeader()->getId());
            $this->assertTrue(
                $this->isMatchingOrderLine($orderLine, $linesData),
                sprintf(
                    'OrderLine with id=%d and description="%s" found in database mock is not expected.',
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
    public static function dataProviderStoreNew(): array
    {
        return [
            'explicit-loading-address-no-lines' => [
                'expectedId' => 5,
                'headerData' => [
                    'customerId' => self::CUSTOMER_ID,
                    'number' => '123/12345678/456',
                    'status' => OrderStatusEnum::NEW,
                    'quantityTotal' => 0,
                    'loadingDate' => new DateTime('2024-06-03'),
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
                'linesData' => [],
            ],
            'fixed-loading-address-and-two-lines' => [
                'expectedId' => 5,
                'headerData' => [
                    'customerId' => self::CUSTOMER_ID,
                    'number' => '123/12345678/456',
                    'status' => OrderStatusEnum::NEW,
                    'quantityTotal' => 8,
                    'loadingDate' => new DateTime('2024-06-03'),
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
                'linesData' => [
                    [
                        'customerId' => self::CUSTOMER_ID,
                        'quantity' => 3,
                        'length' => 120,
                        'width' => 80,
                        'height' => 100,
                        'weightOnePallet' => 20000,
                        'weightTotal' => 60000,
                        'goodsDescription' => 'computers',
                    ],
                    [
                        'customerId' => self::CUSTOMER_ID,
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
     * @noinspection PhpUnhandledExceptionInspection
     */
    #[Test]
    public function createOrderWithRollback(): void
    {
        $order = new Order(self::createFakeObject(OrderHeader::class, []));

        $this->expectException(Exception::class);

        $this->entityManager->method('flush')->willThrowException(new Exception('Simulated DB failure'));

        $this->entityManager->expects(self::once())->method('rollback');

        $this->sut->storeNew($order);
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    #[Test, DataProvider('dataProviderGet')]
    public function get(
        int $id,
        bool $withLines,
        bool $withSsccs,
        bool $expectedOrder,
        int $expectedLinesCount,
        int $expectedSsccsCount,
    ): void {
        $header = self::createFakeObject(OrderHeader::class, ['id' => 10]);
        $this->headerRepository->loadStore([['id' => 10]]);

        $this->lineRepository->loadStore([
            ['id' => 1, 'orderHeader' => $header],
            ['id' => 2, 'orderHeader' => $header],
            ['id' => 3, 'orderHeader' => self::createFakeObject(OrderHeader::class, ['id' => 15])],
        ]);

        $this->ssccRepository->loadStore([
            ['id' => 1, 'orderHeader' => $header],
            ['id' => 2, 'orderHeader' => self::createFakeObject(OrderHeader::class, ['id' => 15])],
        ]);

        $result = $this->sut->get($id, $withLines, $withSsccs);

        if ($expectedOrder) {
            self::assertInstanceOf(Order::class, $result);
            self::assertInstanceOf(OrderHeader::class, $result->getHeader());
            self::assertCount($expectedLinesCount, $result->getLines());
            self::assertCount($expectedSsccsCount, $result->getSsccs());
        } else {
            self::assertNull($result);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function dataProviderGet(): array
    {
        return [
            'null' => [
                'id' => 999,
                'withLines' => false,
                'withSsccs' => false,
                'expectedOrder' => false,
                'expectedLinesCount' => 0,
                'expectedSsccsCount' => 0,
            ],
            'only-header' => [
                'id' => 10,
                'withLines' => false,
                'withSsccs' => false,
                'expectedOrder' => true,
                'expectedLinesCount' => 0,
                'expectedSsccsCount' => 0,
            ],
            'with-lines' => [
                'id' => 10,
                'withLines' => true,
                'withSsccs' => false,
                'expectedOrder' => true,
                'expectedLinesCount' => 2,
                'expectedSsccsCount' => 0,
            ],
            'with-lines-and-ssccs' => [
                'id' => 10,
                'withLines' => true,
                'withSsccs' => true,
                'expectedOrder' => true,
                'expectedLinesCount' => 2,
                'expectedSsccsCount' => 1,
            ],
        ];
    }
}
