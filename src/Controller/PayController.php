<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use \DateTimeInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/pay')]
class PayController extends AbstractController
{
    #[Route('/', name: 'app_pay')]
    public function index(SluggerInterface $slugger, Request $request, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $user->emptyCart();
        $entityManager->persist($user);
        $entityManager->flush();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor($this->getUser()->getName());
            $comment->setPhotoFilename("nothing");

            $file = $form->get('photoFilename')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $this->getUser()->getName().'-'.uniqid().'.png';

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('comment_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $comment->setPhotoFilename($newFilename);
            }

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('homepage', ['type' => "BURGER"]);
        }

        return $this->render('pay/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
