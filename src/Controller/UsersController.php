<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'users')]
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
        ]);
    }
    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('users/login.html.twig', [
        ]);
    }
}
