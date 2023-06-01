<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CartProccess;

#[Route('/cart')]
class CartController extends AbstractController
{
    #[Route('/', name: 'app_cart')]
    public function index(): Response
    {
        //$pros = new CartProccess();
        $sum = 0;
        foreach ($this->getUser()->getCart() as $key => $value) {
            $sum += $value->getPrice();
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $this->getFactorisation($this->getUser()->getCart()),
            'sum' => $sum
        ]);
    }

    function getFactorisation($cart){
        $tab_id = [];
        $newCart = [];
        foreach ($cart as $key => $value) {
            if(in_array($value->getId(), $tab_id)){
                for ($i=0; $i < count($cart); $i++) { 
                    if($newCart[$i][0].getId() == $value->getId()){
                        $newCart[$i][1] += 1;
                        break;
                    }
                }
            }else{
                $newCart[] = [$value, 1]; 
                $tab_id[] = $value->getId();
            }
        }
        return $newCart;
    }
}
