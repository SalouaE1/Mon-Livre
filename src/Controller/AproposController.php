<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AproposController extends AbstractController
{
    #[Route('/qui-sommes-nous', name: 'app_apropos')]
    public function index(): Response
    {
        return $this->render('apropos/index.html.twig');
    }
}
