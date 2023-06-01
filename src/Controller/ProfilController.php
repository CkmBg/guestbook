<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserUpdateFormType;
use App\Form\PasswordFormType;
use Symfony\Component\HttpFoundation\Request;


#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('', name: 'app_profil')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);

        $formp = $this->createForm(PasswordFormType::class, $user);
        $formp->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }

        if ($formp->isSubmitted() && $formp->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
        }


        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
            'formp' => $formp->createView()
        ]);
    }
}
