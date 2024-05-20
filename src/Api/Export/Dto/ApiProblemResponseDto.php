<?php

declare(strict_types=1);

namespace App\Api\Export\Dto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\ConstraintViolation;

class ApiProblemResponseDto
{
    #[OA\Property(example: 'https://symfony.com/errors/validation')]
    #[OA\Property(example: 'order/validator')]
    public string $type;

    #[OA\Property(example: 'Validation Failed')]
    public string $title;

    #[OA\Property(example: '422')]
    public string $status;

    #[OA\Property(example: 'city: This value should be of type string.')]
    public ?string $detail;

//    /**
//     * @var ConstraintViolation[]|null $violations
//     */
//    #[OA\Property(example: 'city: This value should be of type string.', ref: new OA\Schema(type: 'array', items: new OA\Items(new Model(type: ConstraintViolation::class))))]
//    public ?array $violations;
}
