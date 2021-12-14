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
                /** @var \App\Entity\User $user */
                $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                ['FromUserId' => $user->getId()]
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

        $form = $this->createForm(SendMessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // $userId = $user->getId();
            //Build the message and save it in the database
            // $message = $form->getData();
            $message->setToUserId($form->getData('ToUserId'));
            $message->setText($form->getData('Text'));
            $message->setAttachFile($form->getData('AttachFile'));

            $message->setFromUserId($user->getId());
            $message->setTimestamp($date);
            $message->setIsRead(false);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('inbox_messages');
        }

        return $this->renderForm('messages/new_messages.html.twig', [
            'form' => $form,
        ]);
    }
}
