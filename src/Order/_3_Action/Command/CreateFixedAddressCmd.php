<?php

declare(strict_types=1);

namespace App\Order\_3_Action\Command;

use App\Auth\_2_Export\RoleValidatorInterface;
use App\CommonInfrastructure\Api\ApiProblemException;
use App\CommonInfrastructure\DatabaseService;
use App\CommonInfrastructure\GenericDtoValidator;
use App\Customer\_2_Export\CustomerBagInterface;
use App\Customer\_2_Export\GetCustomerInterface;
use App\Order\_2_Export\Command\CreateFixedAddressInterface;
use App\Order\_2_Export\Dto\FixedAddress\CreateFixedAddressDto;
use App\Order\_3_Action\Entity\FixedAddress;
use App\Order\_3_Action\Validator\FixedAddressValidator;
use App\Order\_4_Infrastructure\Repository\FixedAddressRepository;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class CreateFixedAddressCmd implements CreateFixedAddressInterface
{
    public function __construct(
        private readonly FixedAddressRepository $addressRepository,
        private readonly FixedAddressValidator $validator,
        private readonly GenericDtoValidator $dtoValidator,
        private readonly LoggerInterface $logger,
        private readonly DatabaseService $databaseService,
        private readonly GetCustomerInterface $customerQuery,
        private readonly CustomerBagInterface $customerBag,
        private readonly RoleValidatorInterface $roleValidator,
    ) {
    }

    /**
     * @throws ApiProblemException
     * @throws DBALException
     * @throws ValidationFailedException
     */
    public function createFixedAddress(CreateFixedAddressDto $dto): int
    {
        $this->dtoValidator->validate($dto, __FUNCTION__);
        $this->roleValidator->validateHasRole('ROLE_ADMIN');

        $customerDto = $this->customerQuery->getCustomer($dto->customerId);

        $this->databaseService->switchDatabase($customerDto->dbNameSuffix);

        $this->validator->validateExternalIdNotUsed($dto->externalId);

        $address = new FixedAddress();
        $address
            ->setExternalId($dto->externalId)
            ->setNameCompanyOrPerson($dto->nameCompanyOrPerson)
            ->setAddress($dto->address)
            ->setCity($dto->city)
            ->setZipCode($dto->zipCode);

        $this->addressRepository->save($address, true);

        $this->databaseService->switchDatabase($this->customerBag->getDatabaseSuffix());

        $this->logger->info('Created fixed address with id {id} for customer with id {custId}.', ['id' => $address->getId(), 'custIs' => $dto->customerId]);

        return $address->getId();
    }
}
