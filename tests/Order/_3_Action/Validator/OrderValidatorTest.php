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
use RepositoryMock\RepositoryMockTrait;

class OrderValidatorTest extends TestCase
{
    use RepositoryMockTrait;

    private OrderValidator $sut;

    private MockObject|OrderHeaderRepository $orderRepository;
    private MockObject|UserBagInterface $userBag;

    private const CUSTOMER_ID = 1;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderHeaderRepository::class);
        $this->userBag = $this->createMock(UserBagInterface::class);

        $this->userBag->method('getCustomerId')->willReturn(self::CUSTOMER_ID);

        $this->sut = new OrderValidator($this->orderRepository, $this->userBag);
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
    #[DataProvider('dataProviderValidateHasAccessById')]
    public function validateHasAccessById(
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
