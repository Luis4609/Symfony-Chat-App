<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\NewMessageType;
use App\Repository\MessagesRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
 
class MessagesController extends AbstractController
{
    #[Route('/', name: 'messages')]
    public function index(): Response
    {
        return $this->render('messages/index.html.twig', [
            'controller_name' => 'Messages Controller',
        ]);
    }
    #[Route('/inbox', name: 'inbox_messages')]
    public function inbox(MessagesRepository $messagesRepository): Response
    {
        $number = random_int(0, 100);
        $messages = $messagesRepository
            ->findAll();
        return $this->render('messages/inbox.html.twig', [
            'controller_name' => 'Inbox Controller',
            'number' => $number,
            'messages' => $messages,
        ]);
    }
    #[Route('/outbox', name: 'outbox_messages')]
    public function outbox(): Response
    {
        return $this->render('messages/outbox.html.twig', [
            'controller_name' => 'MessagesController',
        ]);
    }
    #[Route('/info_message/{id}', name: 'info_message')]
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
    #[Route('/new_message', name: 'new_message')]
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {

        $date = new \DateTime('@' . strtotime('now'));

        $entityManager = $doctrine->getManager();

        $message = new Messages();
        $message->setFromUserId(2);
        $message->setToUserId(3);
        $message->setIsRead(0);
        $message->setText('Hola amigos, test desde Symfony2.');
        $message->setTimestamp($date);

        $entityManager->persist($message);
        $entityManager->flush();

        $errors = $validator->validate($message);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $form = $this->createForm(NewMessageType::class, $message);

        return $this->renderForm('messages/new_messages.html.twig', [
            'form' => $form,
            'controller_name' => 'MessagesController',
        ]);
        // return new Response('Saved new message with id ' . $message->getId());
        // return $this->render('messages/new_messages.html.twig', [
        //     'controller_name' => 'MessagesController',
        // ]);
    }
}
