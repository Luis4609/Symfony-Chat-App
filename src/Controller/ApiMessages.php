<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Messages;
use App\Form\SendMessageType;
use App\Repository\MessagesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @Route("/api_messages")
 */
class ApiMessagesController extends AbstractController
{
    /**
     * @Route("/", name="api_messages")
     */
    public function index(MessagesRepository $messagesRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                ['FromUserId' => $user->getId()]
            );
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'Messages Controller',
            'messages' => $messages,
        ]);
    }
    /**
     * @Route("/inbox", name="api_inbox")
     */
    public function inbox(MessagesRepository $messagesRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                ['FromUserId' => $user->getId()]
            );
        //Convert to JSON
        $jsonMessages = json_encode($messages);

        // creates a simple Response with a 200 status code (the default)
        $response = new Response($jsonMessages, Response::HTTP_OK);
        return $response;
    }
}
