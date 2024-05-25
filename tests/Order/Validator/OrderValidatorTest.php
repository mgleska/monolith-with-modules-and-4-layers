<?php

declare(strict_types=1);

namespace App\Tests\Order\Validator;

use App\Api\Export\ApiProblemException;
use App\Auth\Export\UserBag;
use App\Order\Entity\Order;
use App\Order\Repository\OrderRepository;
use App\Order\Validator\OrderValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderValidatorTest extends TestCase
{
    private OrderValidator $sut;

    private MockObject|OrderRepository $orderRepository;
    private MockObject|UserBag $userBag;

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->userBag = $this->createMock(UserBag::class);

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
        }
        else {
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
        int    $countResult,
        string $expected
    ): void {
        $this->orderRepository->method('count')->willReturn($countResult);

        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        }
        else {
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
}
