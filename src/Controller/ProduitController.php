<?php

namespace App\Controller;

use App\Entity\Produit;

use App\Form\ProduitType;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/client/dashboard', name: 'dashboard_client')]
    public function dashboard(ProduitRepository $produitRepository, ClientRepository $clientRepository): Response

    {
        $client = $clientRepository->find($this->getUser()->getId());

        $produits = $produitRepository->findBy(['client' => $client]);

        return $this->render('client/dashboard.html.twig', [

            'client' => $client,
            'produits' => $produits
        ]);
    }
    #[Route('/client/parcourir', name: 'parcourir')]
    public function parcourir(ProduitRepository $produitRepository, ClientRepository $clientRepository): Response

    {
        $client = $clientRepository->find($this->getUser()->getId());

        $produits = $produitRepository->findBy(['client' => $client, "statut"=>true]);

        return $this->render('client/parcourir.html.twig', [

            'client' => $client,
            'produits' => $produits
        ]);
    }
    #[Route('/client/dashboard/produit/{idProduit}', name: 'produit_create_modify')]
public function updateProduit(
    $idProduit = 0,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): RedirectResponse | Response {
        $produit =$idProduit == 0 ? new Produit() : $produitRepository->find($idProduit);

        $form = $this->createForm(ProduitType::class,$produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($produit->getClient() == null) {
                $produit->setClient($this->getUser());

            }
            $produit->setDate(new \DateTime());
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_client');
        }

        return $this->render('client/formProduit.html.twig',[
            'form' => $form->createView()
        ]);


    }
    #[Route('/client/dashboardCheck/produit/{idProduit}', name: 'dashboard_check')]

    public function checkStatut(
        $idProduit,
        ProduitRepository $produitRepository,
        EntityManagerInterface $entityManager,
        $status =0,
        Request $request
    ): Response{

        $produit = $produitRepository->find($idProduit);

        $produit->setStatut(!$produit->getStatut());
        $entityManager->persist($produit);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard_client');

    }
    #[Route('/client/dashboard/delete/{idProduit}', name:'produit_delete')]
        public function  deleteProduit(
    $idProduit,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
): RedirectResponse |Response {
        $produit = $produitRepository->find($idProduit);

        if($this->getUser() == $produit->getUser()) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }
        return $this->redirectToRoute('dashboard_client');
    }

}
