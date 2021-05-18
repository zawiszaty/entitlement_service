<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HealtcheckController extends AbstractController
{
    #[Route('/healtcheck', name: 'healtcheck')]
    public function healtcheckAction(): Response
    {
        return new Response('healtcheck');
    }
}
