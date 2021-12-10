<?php

namespace App\Controller;

use App\Repository\UserRepository;
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
        return $this->render('users/index.html.twig', []);
    }
    // /**
    //  * @Route("/login", name="login")
    //  */
    // public function login(): Response
    // {
    //     return $this->render('users/login.html.twig', [
    //     ]);
    // }
    /**
     * @Route("/friends", name="users_friends")
     */
    public function friends(UserRepository $userRepository): Response
    {
        $friendList = $userRepository
        ->findBy(
            ['email' => 'luis@gmail.com']
        );
        return $this->render('users/friends.html.twig', [
            'friends' => $friendList,
        ]);
    }
}
