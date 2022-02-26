<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MessagesRepository;
use App\Repository\UserRepository;
use App\Entity\Messages;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function index(MessagesRepository $messagesRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $messages = $messagesRepository
            ->findBy(
                // ['FromUserId' => $user->getId()]
                ['FromUserId' => 1]
            );
        //Convert to JSON 
        $jsonMessages = json_encode($messages);

        // creates a simple Response with a 200 status code (the default)
        // $response = new Response($jsonMessages, Response::HTTP_OK);

        // returns '{"username":"jane.doe"}' and sets the proper Content-Type header
        // return $this->json(['username' => 'jane.doe']);

        // the shortcut defines three optional arguments
        // return $this->json($data, $status = 200, $headers = [], $context = []);

        $response = new Response();
        $response->setContent(json_encode([
            'data' => $jsonMessages,
        ]));
        $response->headers->set('Content-Type', 'application/json');

        $response = new JsonResponse();
        $response->setData(['data' =>  $jsonMessages]);

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
            'response' => $response,
            'json' => $jsonMessages,
            'messages' => $messages
        ]);
    }
}
