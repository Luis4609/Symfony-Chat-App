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
            ->findBy(
                ['FromUserId' => '2']
            );
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
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request): Response
    {

        $date = new \DateTime('@' . strtotime('now'));

        $entityManager = $doctrine->getManager();

        $message = new Messages();
        
        $form = $this->createForm(SendMessageType::class, $message);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('inbox');
        }

        return $this->renderForm('messages/new_messages.html.twig', [
            'form' => $form,
            'controller_name' => 'MessagesController',
        ]);
    }
}
