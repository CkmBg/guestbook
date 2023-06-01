<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FoodRepository;
use App\Repository\CommentRepository;
use Twig\Environment;
use App\Entity\Food;
use App\Entity\FoodComment;
use App\Form\FoodCommentType;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use \DateTimeInterface;


#[Route('/home')]
class BurgerController extends AbstractController
{
    #[Route('/food/{type}', name: 'homepage', requirements: ['type' => 'BURGER|FRITES|BOISSON|DESSERT'])]
    public function index($type, FoodRepository $foodRepository, CommentRepository $commentRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $pagination = $paginator->paginate(
            $foodRepository->findByCategorie($type),
            $request->query->get('page', 1),
            2
        );

        return $this->render('food/index.html.twig', [
                    'pagination' => $pagination,
                    'type' => $type,
                    'comments' => $commentRepository->findAll()
                ]);
    }


    #[Route('/food/{id}', name: 'food', requirements: ['id' => '\d+'])]
    public function show(Request $request, Food $food, FoodRepository $foodRepository, EntityManagerInterface $entityManager): Response
    {
        $foodComment = new FoodComment();
        $form = $this->createForm(FoodCommentType::class, $foodComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $foodComment->setUser($this->getUser());
            $foodComment->setFood($food);
            $foodComment->setCreationDate(new \DateTime());

            $entityManager->persist($foodComment);
            $entityManager->flush();
        }

        return $this->render('food/show.html.twig', [
            'foods' => $foodRepository->findAll(),
            'food' => $food,
            'form' => $form->createView(),
            'comment' => $food->getFoodComment()
        ]);
    }


    #[Route('/addfood/{id}', name: 'food_add', requirements: ['id' => '\d+'])]
    public function add(Request $request, Food $food, FoodRepository $foodRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user->addCart($food);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('homepage', ['type' => $food->getCategorie()]);
    }
}
