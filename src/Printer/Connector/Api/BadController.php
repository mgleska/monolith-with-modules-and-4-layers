<?php

declare(strict_types=1);

namespace App\Printer\Connector\Api;

/*
 * To see how module boundary checker works, just remove all // comment markers and run "composer run-script check"
 */

use App\Printer\Export\Dto\PrintLabelDto;
//use App\Printer\Action\Command\PrintLabelCmd;
//use App\Order\_1_Connector\Api\OrderController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class BadController extends AbstractController
{
    #[Route(path: '/print/print', name: 'command-create-address', methods: ['POST'], format: 'json')]
    public function printLabel(
        #[MapRequestPayload] PrintLabelDto $dto,
        //PrintLabelCmd $service
    ): void {
        //$service->printLabel($dto, true);
    }
}
