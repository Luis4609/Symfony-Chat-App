<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="users_index")
     */
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('users/index.html.twig', [
            'user' => $user,
        ]);
    }
    //TODO: get friends ADD: friends to the database
    /**
     * @Route("/friends", name="users_friends")
     */
    public function friends(UserRepository $userRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // $friendList = $userRepository
        //     ->findBy(
        //         ['email' => $user->getId()]
        //     );
        $getAllUsers = $userRepository->findAll();

        return $this->render('users/friends.html.twig', [
            'contacts' => $getAllUsers,
        ]);
    }

    /**
     * @Route("/profile/{id}", name="users_profile")
     */
    public function profile(User $user, UserRepository $userRepository): Response
    {
        /** @var \App\Entity\User $user */
        // $user = $this->getUser();

        $userProfile = $userRepository->findBy([
            'id' => $user->getId()
        ]);

        return $this->render('users/profile.html.twig', [
            'user' => $userProfile[0]->getEmail(),
            'data_user' => $user
        ]);
    }
    /**
     * @Route("/edit-profile/{id}", name="users_edit_profile")
     */
    public function editProfile(UserRepository $userRepository, ManagerRegistry $doctrine, Request $request, User $user, FileUploader $fileUploader): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();

        $userEdit = $entityManager->getRepository(User::class)->find($user->getId());


        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $userEdit->setFirstName($_POST['firstName']);
            $userEdit->setLastName($_POST['lastName']);
            $userEdit->setAge($_POST['age']);
            $userEdit->setAddress($_POST['address']);
            
            /** @var UploadedFile $brochureFile */
            // $brochureFile = $_POST['fileToUpload'];
            $brochureFile = $_FILES["fileToUpload"]["name"];
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $userEdit->setAvatar($brochureFileName);
            }

            $entityManager->persist($userEdit);
            $entityManager->flush();

            return $this->redirectToRoute('messages_index');
        }

        return $this->render('users/edit-profile.html.twig', [
            'user' => $user
        ]);
    }
}
