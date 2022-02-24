<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\EditProfileType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

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
        // $package = new Package(new StaticVersionStrategy('v1'));
        // $package->getUrl('/abstract_blue.png');

        /** @var \App\Entity\User $user */
        // $user = $this->getUser();

        $userProfile = $userRepository->findBy([
            'id' => $user->getId()
        ]);

        return $this->render('users/profile.html.twig', [
            'user' => $userProfile[0]->getEmail(),
        ]);
    }
    /**
     * @Route("/edit-profile/{id}", name="users_edit_profile")
     */
    public function editProfile(UserRepository $userRepository, ManagerRegistry $doctrine, Request $request): Response
    {
        // /** @var \App\Entity\User $user */
        // $user = $this->getUser();
        $entityManager = $doctrine->getManager();

        $user = new User();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users_index');
        }

        return $this->render('users/edit-profile.html.twig', [
            //'user' => $user,
            'form' => $form,
        ]);
    }
}
