<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
    /**
     * @Route("/users")
     */
class UsersController extends AbstractController
{
     /**
     * @Route("/", name="users_index")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
        ]);
    }
    // /**
    //  * @Route("/login", name="login")
    //  */
    // public function login(): Response
    // {
    //     return $this->render('users/login.html.twig', [
    //     ]);
    // }
}
