<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\GroupsType;
use App\Entity\Groups;

class GroupsController extends AbstractController
{

    /**
     * @Route("/new_group", name="new_groups")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $group = new Groups();
        $entityManager = $doctrine->getManager();
        
        $form = $this->createForm(GroupsType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $group->$form->getData();

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('inbox_messages');
        }
        return $this->render('groups/index.html.twig', [
            'form' => $form,
        ]);
    }
}
