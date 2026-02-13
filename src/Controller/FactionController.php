<?php

namespace App\Controller;

use App\Entity\Faction;
use App\Repository\FactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FactionController extends AbstractController
{

    public function __construct(private FactionRepository $factionRepository){}

     #[Route('/api/faction', name: 'app_faction', methods: ['POST'])]
    public function createFaction(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(["status" => "error", "message" => "données vides"]);
        }

        $user = $this->userRepository->findOneBy(["token" => $data["token"]]);
        if (!$user) {
            return $this->json(["status" => "error", "message" => "token invalide"]);
        }

        if ($user->getCredits() < 1000) {
            return $this->json(["status" => "error", "message" => "crédits insuffisants"]);
        }

        $faction = new Faction();

        $faction->setName($data["name"]);
        $faction->setDescription($data["description"]);
        $faction->setPower(0);
        $faction->setChef($user);

        $user->setCredits($user->getCredits() - 1000);

        $em->persist($faction);
        $em->flush();

        return $this->json(["status" => "ok", "message" => "Faction créée"]);
    }

    #[Route('/api/factions', name: 'app_factions', methods: ['GET'])]
    public function factions(): Response
    {
        $factions = $this->factionRepository->findAll();

        $result = array_map(function ($faction) {
            return [
                'id' => $faction->getId(),
                'name' => $faction->getName(),
                'description' => $faction->getDescription(),
                'power' => $faction->getPower(),
                'chef' => $faction->getChef()?->getEmail(),
            ];
        }, $factions);

        return $this->json([
            'status' => 'ok',
            'result' => $result,
        ]);
    }

    #[Route('/api/faction/join/{id}', name: 'app_faction_join', methods: ['POST'])]
    public function joinFaction(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->findOneBy(["token" => $data["token"]]);
        $faction = $this->factionRepository->find($id);

        if (!$user || !$faction) {
            return $this->json(["status" => "error", "message" => "not found"]);
        }

        $user->setFaction($faction);
        $faction->setPower($faction->getPower() + 1);

        $em->flush();

        return $this->json(["status" => "ok", "message" => "Faction rejointe"]);
    }

    #[Route('/api/faction/{id}', name: 'app_faction_delete', methods: 'DELETE')]
    public function deleteFaction(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userRepository->findOneBy(["token" => $data["token"]]);
        $faction = $this->factionRepository->find($id);

        if (!$user || !$faction) {
            return $this->json(["status" => "error", "message" => "not found"]);
        }

        if ($faction->getChef() !== $user && $user->getRole() !== "ADMIN") {
            return $this->json(["status" => "error", "message" => "accès refusé"]);
        }

        $em->remove($faction);
        $em->flush();

        return $this->json(["status" => "ok", "message" => "Faction dissoute"]);
    }
}