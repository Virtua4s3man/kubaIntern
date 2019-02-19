<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/welcome", name="welcome")
     */
    public function index()
    {
        $session = new Session(new NativeSessionStorage(), new NamespacedAttributeBag());
        $session->set('wishlist/prod1', 'ok');
        $session->set('user/dono', 'user');

        dump($session);

        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'Welcome',
            'date' => new \DateTime(),
        ]);
    }
}
