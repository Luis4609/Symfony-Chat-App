<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Messages;
use App\Form\SendMessageType;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/messages")
 */
class MessagesController extends AbstractController
{

    /**
     * @Route("/", name="messages_index")
     */
    public function index(MessagesRepository $messagesRepository, UserRepository $userRepository): Response
    {

        if (isset($_GET['errorMessage'])) {
            return new Response(
                '<html><body> ' . $_GET['errorMessage'] . '</body></html>'
            );
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // $messages = $messagesRepository
        //     ->findBy(
        //         ['ToUserId' => $user->getId()]
        //     );

        $messages = $messagesRepository->createQueryBuilder('m')
            ->andWhere("m.ToUserId = :val")
            ->setParameter('val', $user->getId())
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        $users = $userRepository->findAll();

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
            'users' => $users
        ]);
    }

    // /**
    //  * @Route("/inbox", name="inbox_messages")
    //  */
    // public function inbox(MessagesRepository $messagesRepository): Response
    // {
    //     /** @var \App\Entity\User $user */
    //     $user = $this->getUser();
    //     $messages = $messagesRepository
    //         ->findBy(
    //             ['ToUserId' => $user->getId()]
    //         );
    //     //Convert to JSON
    //     $jsonMessages = json_encode($messages);

    //     // creates a simple Response with a 200 status code (the default)
    //     $response = new Response($jsonMessages, Response::HTTP_OK);
    //     return $response;
    // }
    /**
     * @Route("/outbox", name="outbox_messages")
     */
    public function outbox(MessagesRepository $messagesRepository, UserRepository $userRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $users = $userRepository->findAll();

        // $messages = $messagesRepository
        //     ->findBy(
        //         ['FromUserId' => $user->getId()]
        //     );

        $messages = $messagesRepository->createQueryBuilder('m')
            ->andWhere("m.FromUserId = :val")
            ->setParameter('val', $user->getId())
            ->orderBy('m.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('messages/outbox.html.twig', [
            'messages' => $messages,
            'users' => $users
        ]);
    }
    /**
     * @Route("/info_message/{id}", methods="GET", name="info_message")
     */
    public function infoMessage(Messages $message, ManagerRegistry $doctrine, MessagesRepository $messagesRepository, Request $request): Response
    {
        if (!$message) {
            throw $this->createNotFoundException(
                'No product found for id ' . $message->getId()
            );
        }
        $entityManager = $doctrine->getManager();

        $messageToUpdate = $entityManager->getRepository(Messages::class)->find($message->getId());

        if (!$messageToUpdate) {
            throw $this->createNotFoundException(
                'No product found for id ' . $message->getId()
            );
        }

        $messageToUpdate->setIsRead(true);
        $entityManager->flush();

        return $this->render('messages/info_message.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/new_message", name="new_message")
     */
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator, UserRepository $userRepository): Response
    {
        //Current date for the message
        $date = new \DateTime('@' . strtotime('now'));

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $users = $userRepository->findAll();

        $entityManager = $doctrine->getManager();

        $message = new Messages();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if ($user->getId() == $_POST['username']) {
                return $this->redirectToRoute('messages_error?errorMessage=Cant send this message');
            }

            // * Get id of the user from the email
            $toUserId = $userRepository
                ->findBy(
                    ['email' => $_POST['username']]
                );
            $message->setToUserId($toUserId[0]->getId());

            //DATA FROM FORM
            // $message->setToUserId($_POST['username']);
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
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new_message_participants", name="new_message_participants")
     */
    public function newMessageParticipants(ManagerRegistry $doctrine, ValidatorInterface $validator, UserRepository $userRepository): Response
    {
        //Current date for the message
        $date = new \DateTime('@' . strtotime('now'));

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $users = $userRepository->findAll();

        $entityManager = $doctrine->getManager();

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['messageParticipants'])) {
            foreach ($_POST['messageParticipants'] as $participant) {
                $message = new Messages();
                // * Get id of the user from the email
                $toUserId = $userRepository
                    ->findBy(
                        ['email' => $participant]
                    );
                $message->setToUserId($toUserId[0]->getId());
                $message->setText($_POST['message']);
                $message->setFromUserId($user->getId());
                $message->setTimestamp($date);
                $message->setIsRead(false);
                $entityManager->persist($message);
                $entityManager->flush();
            }
            return $this->redirectToRoute('messages_index');
        }

        return $this->renderForm('messages/new_messages_participants.html.twig', [
            'users' => $users,
        ]);
    }
}
