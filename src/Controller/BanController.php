<?php

namespace App\Controller;

use App\Entity\Ban;
use App\Repository\BanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BanController extends AbstractController
{

    public function __construct(private BanRepository $banRepository){}

    #[Route('/api/admin/ban', name: 'app_ban', methods: 'POST')]
    public function ban(): Response
    {
       
        $data = json_decode($request->getContent(), true);

        $user = $userRepo->find($data['userId']);
        if (!$user) {
            return $this->json(['message' => 'Error']);
        }

        $ban = new Ban();
        
        $ban->setUserCible($user);
        $ban->setRaison($data['raison']);
        $ban->setDateFin(new \DateTime($data['dateFin']));
        $ban->setIsActive(true);

        $em->persist($ban);
        $em->flush();

        return $this->json(['message' => 'User banned']);
    }

    #[Route('/bans', name: 'app_bans', methods: ['GET'])]
    public function bans(BanRepository $banRepository): Response
    {
        $bans = $banRepo->findBy(['isActive' => true]);

        $result = [];
        foreach ($bans as $ban) {
            $result[] = [
                'user' => $ban->getUserCible()->getEmail(),
                'raison' => $ban->getRaison(),
            ];
        }

        return $this->json($result);
    }
}