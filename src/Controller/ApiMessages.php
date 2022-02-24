<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Messages;
use App\Form\SendMessageType;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
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
    //TODO: create APIs to get the messages for user
    /**
     * @Route("/inbox/{id}", name="api_inbox")
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

        // returns '{"username":"jane.doe"}' and sets the proper Content-Type header
        // return $this->json(['username' => 'jane.doe']);

        // the shortcut defines three optional arguments
        // return $this->json($data, $status = 200, $headers = [], $context = []);
        return $response;
    }
    /**
     * @Route("/outbox/{id}", name="api_outbox")
     */
    public function outbox(MessagesRepository $messagesRepository): Response
    {
        // ? I can get the id of the user from the logged user or passed in the url.
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
    /**
     * @Route("/info_message/{id}", name="api_outbox")
     */
    public function infoMessage(Messages $message, MessagesRepository $messagesRepository, ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        // ? I can get the id of the user from the logged user or passed in the url.
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();

        //Get email of the user that sends the message
        $getFromUserId = $messagesRepository->findBy([
            'id' => $message->getId()
        ]);

        $fromUserEmail = $userRepository->findBy([
            'id' => $getFromUserId[0]->getFromUserId()
        ]);

        //Update isRead in database
        $messageToUpdate = $entityManager->getRepository(Messages::class)->find($message->getId());
        $messageToUpdate->setIsRead(true);
        $entityManager->flush();

        //Convert to JSON
        $jsonMessages = json_encode($message);
        $jsonEmailUser = json_encode($fromUserEmail);

        // creates a simple Response with a 200 status code (the default)
        $response = new Response($jsonMessages, Response::HTTP_OK);
        return $response;
    }
}
