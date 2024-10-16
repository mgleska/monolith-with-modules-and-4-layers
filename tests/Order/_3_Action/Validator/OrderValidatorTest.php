<?php

declare(strict_types=1);

namespace App\Tests\Order\_3_Action\Validator;

use App\Api\_2_Export\ApiProblemException;
use App\Auth\_2_Export\UserBagInterface;
use App\Order\_2_Export\Dto\Order\OrderAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Entity\Order;
use App\Order\_3_Action\Entity\OrderHeader;
use App\Order\_3_Action\Validator\OrderValidator;
use App\Order\_4_Infrastructure\Repository\OrderHeaderRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderValidatorTest extends TestCase
{
    private OrderValidator $sut;

    private MockObject|OrderHeaderRepository $orderHeaderRepository;
    private MockObject|UserBagInterface $userBag;

    private const CUSTOMER_ID = 1;
    private const INVALID_CUSTOMER_ID = 2;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderHeaderRepository = $this->createMock(OrderHeaderRepository::class);
        $this->userBag = $this->createMock(UserBagInterface::class);

        $this->userBag->method('getCustomerId')->willReturn(self::CUSTOMER_ID);

        $this->sut = new OrderValidator($this->orderHeaderRepository, $this->userBag);
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
                'order' => new Order(new OrderHeader()),
                'expected' => '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProviderValidateHasAccess')]
    public function validateHasAccess(
        int $customerId,
        string $expected
    ): void {
        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $header = new OrderHeader();
        $header->setCustomerId($customerId);

        $this->sut->validateHasAccess(new Order($header));
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateHasAccess(): array
    {
        return [
            'yes' => [
                'customerId' => self::CUSTOMER_ID,
                'expected' => '',
            ],
            'no' => [
                'customerId' => self::INVALID_CUSTOMER_ID,
                'expected' => 'ORDER_ORDER_NO_ACCESS',
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProviderValidateHasAccessById')]
    public function validateHasAccessById(
        int $countResult,
        string $expected
    ): void {
        $this->orderHeaderRepository->method('count')->willReturn($countResult);

        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateHasAccessById(1);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateHasAccessById(): array
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
        ?FixedAddress $fixedAddress,
        ?OrderAddressDto $addressDto,
        string $expectedExceptionMsg
    ): void {
        if ($expectedExceptionMsg) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expectedExceptionMsg . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateLoadingAddressForCreate($fixedAddress, $addressDto);
    }

    /**
     * @return array<string, array<string, mixed>>
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function dataProviderValidateLoadingAddressForCreate(): array
    {
        return [
            'null-null' => [
                'fixedAddress' => null,
                'addressDto' => null,
                'expectedExceptionMsg' => 'ORDER_CREATE_LOADING_ADDRESS_NOT_SPECIFIED',
            ],
            'set-both' => [
                'fixedAddress' => new FixedAddress(),
                'addressDto' => new OrderAddressDto('', '', '', ''),
                'expectedExceptionMsg' => 'ORDER_CREATE_LOADING_ADDRESS_SPECIFIED_BY_EXTERNAL_ID_AND_BY_VALUE',
            ],
            'fixed-address-not-requested' => [
                'fixedAddress' => null,
                'addressDto' => new OrderAddressDto('', '', '', ''),
                'expectedExceptionMsg' => '',
            ],
            'valid-external-id' => [
                'fixedAddress' => new FixedAddress(),
                'addressDto' => null,
                'expectedExceptionMsg' => '',
            ],
        ];
    }
}
