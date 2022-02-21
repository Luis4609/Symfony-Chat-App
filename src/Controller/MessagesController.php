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
 * @Route("/messages")
 */
class MessagesController extends AbstractController
{

    /**
     * @Route("/", name="messages_index")
     */
    public function index(MessagesRepository $messagesRepository): Response
    {

        if (isset($_GET['errorMessage'])) {
            return new Response(
                '<html><body> ' . $_GET['errorMessage'] . '</body></html>'
            );
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $messages = $messagesRepository
            ->findBy(
                ['ToUserId' => $user->getId()]
            );
        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }
    // /**
    //  * @Route("/{errorMessage}", name="messages_error")
    //  */
    // public function errorMessages(MessagesRepository $messagesRepository): Response
    // {

    //     if (isset($_GET['errorMessage'])) {
    //         return new Response(
    //             '<html><body> ' . $_GET['errorMessage'] . '</body></html>'
    //         );
    //     }
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
    //     $messages = $messagesRepository
    //         ->findBy(
    //             ['FromUserId' => $user->getId()]
    //         );
    //     return $this->render('messages/index.html.twig', [
    //         'messages' => $messages,
    //     ]);
    // }
    /**
     * @Route("/inbox", name="inbox_messages")
     */
    public function inbox(MessagesRepository $messagesRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                ['ToUserId' => $user->getId()]
            );
        //Convert to JSON
        $jsonMessages = json_encode($messages);

        // creates a simple Response with a 200 status code (the default)
        $response = new Response($jsonMessages, Response::HTTP_OK);
        return $response;
    }
    /**
     * @Route("/outbox", name="outbox_messages")
     */
    public function outbox(MessagesRepository $messagesRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                ['FromUserId' => $user->getId()]
            );
        return $this->render('messages/outbox.html.twig', [
            'messages' => $messages,
        ]);
    }
    /**
     * @Route("/info_message/{id}", name="info_message")
     */
    public function infoMessage(Messages $message): Response
    {
        if (!$message) {
            throw $this->createNotFoundException(
                'No product found for id ' . $message->getId()
            );
        }
        return $this->render('messages/info_message.html.twig', [
            'controller_name' => 'Inbox Controller',
            'message' => $message,
            // 'message_time' => $message->getTimestamp(),
        ]);
    }

    /**
     * @Route("/new_message", name="new_message")
     */
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request): Response
    {
        //Current date for the message
        $date = new \DateTime('@' . strtotime('now'));

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();

        $message = new Messages();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if ($user->getId() == $_POST['username']) {
                return $this->redirectToRoute('messages_error?errorMessage=Cant send this message');
            }
            //DATA FROM FORM
            $message->setToUserId($_POST['username']);
            $message->setText($_POST['message']);
            // $message->setAttachFile($_POST['fileToUpload']);

            //Data that is default
            $message->setFromUserId($user->getId());
            $message->setTimestamp($date);
            $message->setIsRead(false);

            //Save new message in database
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('messages_index');
        }

        return $this->renderForm('messages/new_messages.html.twig', [
            // 'form' => $form,
        ]);
    }
}
