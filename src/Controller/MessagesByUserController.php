<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MessagesByUserController extends AbstractController
{
    #[Route('/messages/by/user', name: 'messages_by_user')]
    public function index(): Response
    {
        return $this->render('messages_by_user/index.html.twig', [
            'controller_name' => 'MessagesByUserController',
        ]);
    }

    // private $bookPublishingHandler;

    // public function __construct(BookPublishingHandler $bookPublishingHandler)
    // {
    //     $this->bookPublishingHandler = $bookPublishingHandler;
    // }

    // public function __invoke(Book $data): Book
    // {
    //     $this->bookPublishingHandler->handle($data);

    //     return $data;
    // }
}
