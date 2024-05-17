<?php

declare(strict_types=1);

namespace App\Printer\Controller;

use App\Printer\Export\Dto\PrintLabelDto;
use App\Printer\Service\PrintCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class PrintController extends AbstractController
{
    #[Route(path: '/print-label', name: 'command-print-label', methods: ['POST'], format: 'text')]
    public function creatLabel(
        #[MapRequestPayload] PrintLabelDto $dto,
        PrintCommand $service,
    ): Response
    {
        $label = $service->printLabel($dto->orderId);
        return new Response($label, 200, ['Content-Type' => 'text/plain']);
    }
}
