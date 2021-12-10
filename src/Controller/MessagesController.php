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
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'Messages Controller',
        ]);
    }
    /**
     * @Route("/inbox", name="inbox_messages")
     */
    public function inbox(MessagesRepository $messagesRepository): Response
    {
        $messages = $messagesRepository
            ->findBy(
                ['FromUserId' => '2']
            );
        return $this->render('messages/inbox.html.twig', [
            'controller_name' => 'Inbox Controller',
            'messages' => $messages,
        ]);
    }
    /**
     * @Route("/outbox", name="outbox_messages")
     */
    public function outbox(MessagesRepository $messagesRepository): Response
    {
        $messages = $messagesRepository
            ->findBy(
                ['ToUserId' => '2']
            );
        return $this->render('messages/outbox.html.twig', [
            'messages' => $messages,
        ]);
    }
    /**
     * @Route("/info_message/{id}", name="info_message")
     */
    public function infoMessage(int $id, MessagesRepository $messagesRepository): Response
    {
        // $message = $doctrine->getRepository(Messages::class)->find($id);

        $message = $messagesRepository
            ->find($id);

        if (!$message) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }
        return $this->render('messages/info_message.html.twig', [
            'controller_name' => 'Inbox Controller',
            'message' => $message->getText(),
            // 'message_time' => $message->getTimestamp(),
        ]);
    }
    /**
     * @Route("/new_message", name="new_message")
     */
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request): Response
    {
        //Agregar date al mensaje, no pedirlo en el form
        $date = new \DateTime('@' . strtotime('now'));

        $entityManager = $doctrine->getManager();

        $message = new Messages();

        $form = $this->createForm(SendMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            // $fromUserId = $session -> get(fromUserId);
            // New message 
            // $mymessage =  $_POST['message'];  --> getData
            // $date = date('Y-m-d H:i:s');
            // $data = [
            //     'fromuserid' => $userId,   -->SESSION[user]
            //     'touserid' => $touserid, --> getData
            //     'mymessage' => $mymessage,  --> getData
            //     'newdate' => $date,  --> $date = date('Y-m-d H:i:s'); se recoge la fecha cuando se ejecuta el POST
            //     'attachfile'  => upload_file(false) --> getData
            // ];
            // tell Doctrine you want to (eventually) save the Message (no queries yet)
            $entityManager->persist($message);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            return $this->redirectToRoute('inbox_messages');
        }

        return $this->renderForm('messages/new_messages.html.twig', [
            'form' => $form,
        ]);
    }
}
