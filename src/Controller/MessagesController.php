<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\Mime\Message;

/**
 * @Route("/messages")
 */
class MessagesController extends AbstractController
{

    /**
     * @Route("/", name="messages_index")
     */
    public function index(MessagesRepository $messagesRepository, UserRepository $userRepository, Request $request): Response
    {
        //TODO check if this work to return json on this controller
        // if ($request->isXmlHttpRequest()) {

        //     // creates a simple Response with a 200 status code (the default)
        //     $response = new Response('Hello ' . $name, Response::HTTP_OK);

        //     // creates a CSS-response with a 200 status code
        //     $response = new Response('<style> ... </style>');
        //     $response->headers->set('Content-Type', 'text/css');
        // }

        //? If a error is set, show an alert
        if (isset($_GET['errorMessage'])) {
            return new Response(
                '<html><body> ' . $_GET['errorMessage'] . '</body></html>'
            );
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        //*Get all the users from the database
        $users = $userRepository->findAll();

        $messages = $messagesRepository->createQueryBuilder('m')
            ->andWhere("m.ToUserId = :val")
            ->setParameter('val', $user->getId())
            ->orderBy('m.Timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        //!Error handling
        if (!$messages) {
            throw $this->createNotFoundException(
                'There was an error, please try again.'
            );
        }

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
            'users' => $users
        ]);
    }
    /**
     * @Route("/outbox", name="outbox_messages")
     */
    public function outbox(MessagesRepository $messagesRepository, UserRepository $userRepository): Response
    {
        //? If a error is set, show an alert
        if (isset($_GET['errorMessage'])) {
            return new Response(
                '<html><body> ' . $_GET['errorMessage'] . '</body></html>'
            );
        }
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        //*Get all the users from the database
        $users = $userRepository->findAll();

        $messages = $messagesRepository->createQueryBuilder('m')
            ->andWhere("m.FromUserId = :val")
            ->setParameter('val', $user->getId())
            ->orderBy('m.Timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        //!Error handling
        if (!$messages) {
            throw $this->createNotFoundException(
                'There was an error, please try again.'
            );
        }

        return $this->render('messages/outbox.html.twig', [
            'messages' => $messages,
            'users' => $users
        ]);
    }
    /**
     * @Route("/info_message/{id}", methods="GET", name="info_message")
     */
    public function infoMessage(Messages $message, ManagerRegistry $doctrine, MessagesRepository $messagesRepository, Request $request, UserRepository $userRepository): Response
    {
        //!Error handling
        if (!$message) {
            throw $this->createNotFoundException(
                'No messsage found for id ' . $message->getId()
            );
        }
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

        return $this->render('messages/info_message.html.twig', [
            'message' => $message,
            'user' => $fromUserEmail[0]
        ]);
    }

    //TODO: make the controller that handles ANSWER A MESSAGE
    /**
     * @Route("/new_message", name="new_message")
     */
    public function newMessage(ManagerRegistry $doctrine, ValidatorInterface $validator, UserRepository $userRepository): Response
    {
        // //* Set de email, if is a response
        // if(isset($_GET['email'])){

        // }

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
            $message->setText($_POST['message']);
            // $message->setAttachFile($_POST['fileToUpload']);

            //Set the data of the message, that the user dont write
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
                //DATA FROM FORM
                $message->setText($_POST['message']);
                // $message->setAttachFile($_POST['fileToUpload']);

                //Set the data of the message, that the user dont write
                $message->setFromUserId($user->getId());
                $message->setTimestamp($date);
                $message->setIsRead(false);

                //Save new message in database
                $entityManager->persist($message);
                $entityManager->flush();
            }
            return $this->redirectToRoute('messages_index');
        }

        return $this->renderForm('messages/new_messages_participants.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/search", methods="GET", name="messages_search")
     */
    public function search(Request $request, MessagesRepository $messagesRepository): Response
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);

        if (!$request->isXmlHttpRequest()) {
            return $this->render('messages/search.html.twig', ['query' => $query]);
        }

        $foundmessages = $messagesRepository->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundmessages as $message) {
            $results[] = [
                'fromUserId' => htmlspecialchars($message->getFromUserId(), \ENT_COMPAT | \ENT_HTML5),
                'toUserId' => htmlspecialchars($message->getToUserId(), \ENT_COMPAT | \ENT_HTML5),
                'text' => htmlspecialchars($message->getText(), \ENT_COMPAT | \ENT_HTML5),
                'date' => $message->getTimestamp(),
                'isRead' => htmlspecialchars($message->getIsRead(), \ENT_COMPAT | \ENT_HTML5),
                'url' => $this->generateUrl('info_message', ['id' => $message->getId()]),
            ];
        }

        return $this->json($results);
    }
}
