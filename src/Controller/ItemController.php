<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ItemController extends AbstractController
{

    public function __construct(private ItemRepository $itemRepository){}

    #[Route('/api/shop', name: 'app_shop', methods: 'GET')]
    public function shop(Request $request, EntityManagerInterface $entityManager): Response
    {

        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $item = $this->itemRepository->findOneBy(["token"=>$token]);

        if(!$item){
            return $this->json(["status"=>"error", "message"=>"not found"]);
        }

        return $this->json(["status"=>"ok", "message"=>"Accès autorisé", "result"=>
        [
            "id"=>$item->getId(),
            "nom"=>$item->getNom(),
            "description"=>$item->geDescription(),
            "prix"=>$item->getPrix(),
            "rarete"=>$item->geRarete()
        ]]);
    }



    #[Route('/api/shop/{codeName}', name: 'app_shop_item', methods: 'POST')]
    public function shop_item(Request $request, EntityManagerInterface $entityManager): Response
    {

        $item = $this->ItemRepository->find($id);

        if (empty($item)) {

            return $this->json(["status" => "error", "message" => "item introuvable"]);

        } else {

            return $this->json(["status" => "ok", "message" => "Item Acheter", "result" => 
            [
                "id"=>$item->getID(),
                "name"=>$item->getName(),
            ]
            ]);
        }
    }


    #[Route('/api/shop/token', name: 'app_shop_token', methods: 'GET')]
    public function token(Request $request, EntityManagerInterface $entityManager): Response
    {

        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $this->userRepository->findOneBy(["token"=>$token]);

        if(!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        return $this->json(["status"=>"ok", "message"=>"connected", "result"=>
            [   
                "id"=>$tiem->getId(),
                "descritpion"=>$item->getDescription(),
                "rarete"=>$item->getRarete()
            ]]);

    }

}
