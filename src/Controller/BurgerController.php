<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BurgerRepository;
use Twig\Environment;
use App\Entity\Burger;

class BurgerController extends AbstractController
{
    #[Route('/home', name: 'homepage')]
    public function index(BurgerRepository $burgerRepository): Response
    {
        return $this->render('burger/index.html.twig', [
                    'burgers' => $burgerRepository->findAll(),
                ]);
    }
    #[Route('/burger/{id}', name: 'burger')]
    public function show(Request $request, Burger $burger, BurgerRepository $burgerRepository): Response
    {
        return $this->render('burger/show.html.twig', [
            'burgers' => $burgerRepository->findAll(),
            'burger' => $burger,
        ]);
    }
}
