<?php

/**
 * @noinspection PhpUnhandledExceptionInspection
 */

declare(strict_types=1);

namespace App\Tests\Order\Validator;

use App\Api\Export\ApiProblemException;
use App\Order\Entity\FixedAddress;
use App\Order\Repository\FixedAddressRepository;
use App\Order\Validator\FixedAddressValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RepositoryMock\RepositoryMockObject;
use RepositoryMock\RepositoryMockTrait;

class FixedAddressValidatorTest extends TestCase
{
    use RepositoryMockTrait;

    private FixedAddressValidator $sut;

    private FixedAddressRepository|RepositoryMockObject $addressRepository;

    protected function setUp(): void
    {
        $this->addressRepository = $this->createRepositoryMock(FixedAddressRepository::class);

        $this->sut = new FixedAddressValidator($this->addressRepository);
    }

    #[Test]
    #[DataProvider('dataProviderValidateExists')]
    public function validateExists(
        ?FixedAddress $address,
        string $expected
    ): void {
        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateExists($address);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateExists(): array
    {
        return [
            'null' => [
                'address' => null,
                'expected' => 'ORDER_FIXEDADDRESS_NOT_FOUND',
            ],
            'valid' => [
                'address' => new FixedAddress(),
                'expected' => '',
            ],
        ];
    }

    #[Test]
    #[DataProvider('dataProviderValidateExternalIdNotUsed')]
    public function validateExternalIdNotUsed(
        int $customerId,
        string $externalId,
        string $expected
    ): void {
        $this->addressRepository->loadStore([
            [
                'id' => 1,
                'customerId' => 10,
                'externalId' => 'WH1',
            ]
        ]);

        if ($expected) {
            $this->expectException(ApiProblemException::class);
            $this->expectExceptionMessageMatches('/^' . $expected . '$/');
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->sut->validateExternalIdNotUsed($customerId, $externalId);
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataProviderValidateExternalIdNotUsed(): array
    {
        return [
            'id-not-used' => [
                'customerId' => 10,
                'externalId' => 'AAA',
                'expected' => '',
            ],
            'different-customer' => [
                'customerId' => 22,
                'externalId' => 'WH1',
                'expected' => '',
            ],
            'conflict' => [
                'customerId' => 10,
                'externalId' => 'WH1',
                'expected' => 'ORDER_FIXEDADDRESS_EXTERNAL_ID_ALREADY_EXIST',
            ],
        ];
    }
}
