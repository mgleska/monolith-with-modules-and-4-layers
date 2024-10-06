<?php

declare(strict_types=1);

namespace App\Tests\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use App\Order\_4_Infrastructure\Repository\OrderRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RepositoryMock\RepositoryMockObject;
use RepositoryMock\RepositoryMockTrait;

class OrderValidatorTest extends TestCase
{
    use RepositoryMockTrait;

    private OrderValidator $sut;

    private MockObject|OrderRepository $orderRepository;
    private MockObject|UserBagInterface $userBag;
    private RepositoryMockObject|FixedAddressRepository $addressRepository;

    private const CUSTOMER_ID = 1;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->userBag = $this->createMock(UserBagInterface::class);
        $this->addressRepository = $this->createRepositoryMock(FixedAddressRepository::class);

        $this->userBag->method('getCustomerId')->willReturn(self::CUSTOMER_ID);

        $this->sut = new OrderValidator($this->orderRepository, $this->userBag, $this->addressRepository);
    }

    #[Test]
    #[DataProvider('dataProviderValidateExists')]
    public function validateExists(
        ?Order $order,
        string $expected
    ): void {
        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateExists($order);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateExists(): array
    {
        return [
            'null' => [
                'order' => null,
                'expected' => 'ORDER_ORDER_NOT_FOUND',
            ],
            'valid' => [
                'order' => new Order(),
                'expected' => '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProviderValidateHasAccess')]
    public function validateHasAccess(
        int $countResult,
        string $expected
    ): void {
        $this->orderRepository->method('count')->willReturn($countResult);

        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateHasAccess(1);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateHasAccess(): array
    {
        return [
            'no-access' => [
                'countResult' => 0,
                'expected' => 'ORDER_ORDER_NOT_FOUND',
            ],
            'valid' => [
                'countResult' => 1,
                'expected' => '',
            ],
        ];
    }


    #[Test]
    #[DataProvider('dataProviderValidateLoadingAddressForCreate')]
    public function validateLoadingAddressForCreate(
        ?string $fixedAddressExternalId,
        ?OrderAddressDto $addressDto,
        ?FixedAddress $expectedAddress,
        string $expectedExceptionMsg
    ): void {
        if ($expectedExceptionMsg) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expectedExceptionMsg . '$/');
        }

        $this->addressRepository->loadStore([
            ['id' => 2, 'customerId' => self::CUSTOMER_ID, 'externalId' => 'HQ'],
        ]);

        $retrievedAddress = $this->sut->validateLoadingAddressForCreate($fixedAddressExternalId, $addressDto);

        $this->assertEquals($expectedAddress, $retrievedAddress);
    }

    /**
     * @return array<string, array<string, mixed>>
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function dataProviderValidateLoadingAddressForCreate(): array
    {
        return [
            'null-null' => [
                'fixedAddressExternalId' => null,
                'addressDto' => null,
                'expectedAddress' => null,
                'expectedExceptionMsg' => 'ORDER_CREATE_LOADING_ADDRESS_NOT_SPECIFIED',
            ],
            'set-both' => [
                'fixedAddressExternalId' => 'WH1',
                'addressDto' => new OrderAddressDto('', '', '', ''),
                'expectedAddress' => null,
                'expectedExceptionMsg' => 'ORDER_CREATE_LOADING_ADDRESS_SPECIFIED_BY_EXTERNAL_ID_AND_BY_VALUE',
            ],
            'fixed-address-not-requested' => [
                'fixedAddressExternalId' => null,
                'addressDto' => new OrderAddressDto('', '', '', ''),
                'expectedAddress' => null,
                'expectedExceptionMsg' => '',
            ],
            'wrong-external-id' => [
                'fixedAddressExternalId' => 'WH1',
                'addressDto' => null,
                'expectedAddress' => null,
                'expectedExceptionMsg' => 'ORDER_CREATE_LOADING_ADDRESS_NOT_FOUND',
            ],
            'valid-external-id' => [
                'fixedAddressExternalId' => 'HQ',
                'addressDto' => null,
                'expectedAddress' => self::createFakeObject(FixedAddress::class, [
                    'id' => 2,
                    'customerId' => self::CUSTOMER_ID,
                    'externalId' => 'HQ'
                ]),
                'expectedExceptionMsg' => '',
            ],
        ];
    }
}
