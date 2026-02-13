<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{

    public function __construct(private UserRepository $userRepository){}

    #[Route('/api/register', name: 'app_register', methods: 'POST')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {   

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"c'est vide"]);
        }

        $user = new User();

        $user->setEmail($data['email']);
        $user->setPseudoMinecraft($data['pseudoMinecraft']);
        $user->setUuidMinecraft($data['uuidMinecraft']);

        $password = md5(uniqid());
        $user->setPassword(md5($data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Inscription réussie']);
    }


    #[Route('/api/login', name: 'app_login', methods: 'POST')]
    public function login(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"c'est vide"]);
        }

        $user = $this->userRepository->findOneBy(["token"=>$data["token"]]);
        if(!$user){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        if(md5($data['password']) == $user->getPassword()){

            return $this->json(["status"=>"ok", "message"=>"login ok", "result"=>[
                "id"=>$user->getId(),
                "email"=>$user->getEmail(),
            ]]);


        } else {

            return $this->json(["status"=>"error", "message"=>"login failed, wrong token"]);

        }

    }




    #[Route('/api/me', name: 'app_me', methods: 'GET')]
    public function me(Request $request, EntityManagerInterface $entityManager): Response
    {

        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $this->userRepository->findOneBy(["token"=>$token]);

        if(!$user){
            return $this->json(["status"=>"error", "message"=>"not found"]);
        }

        return $this->json(["status"=>"ok", "message"=>"Accès autorisé", "result"=>
        [
            "id"=>$user->getId(),
            "email"=>$user->getEmail(),
            "role"=>$user->getRole(),
            "pseudoMinecraft"=>$user->getPseudoMinecraft(),
            "UuidMinecraft"=>$user->getUuidMinecraft(),
            "credits"=>$user->getCredits(),
            "dateInscription"=>$user->getDateInscription(),
        ]]);
    }



    #[Route('/api/me/{id}', name: 'app_me_put', methods: 'PUT')]
    public function me_put(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->userRepository->find($id);

        if (!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data){
            return $this->json(["status"=>"error", "message"=>"donnee vide"]);
        }

        $user->setPseudoMinecraft($data["pseudoMinecraft"]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(["status"=>"ok", "message" => "user updated", "result" => 
            [
                "id"=>$user->gettoken(),
                "email"=>$user->getEmail(),
                "role"=>$user->getRole(),
                "pseudoMinecraft"=>$user->getPseudoMinecraft(),
                "UuidMinecraft"=>$user->getUuidMinecraft(),
                "credits"=>$user->getCredits(),
                "dateInscription"=>$user->getDateInscription()
            ]]);
    }
}