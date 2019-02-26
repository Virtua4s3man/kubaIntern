<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="welcome")
     */
    public function index(TranslatorInterface $translator)
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => $translator->trans('welcome'),
            'date' => new \DateTime(),
        ]);
    }
}
