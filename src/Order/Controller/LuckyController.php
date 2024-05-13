<?php

declare(strict_types=1);

namespace App\Order\Controller;

use App\Auth\Export\UserBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number')]
    public function number(UserBag $userBag): Response
    {
        // $number = random_int(0, 100);

        return new Response(
            // '<html><body>Lucky number: '.$number.'</body></html>'
            $userBag->getUserId() . ' ' . $userBag->getCustomerId()
        );
    }
}
