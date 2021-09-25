<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/register", name="client_register")
     * @param Request $request
     * @param $entityManager
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $registerForm = $this->createForm(RegisterType::class, $client);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            // le mot de passe est automatiquement hashé
            $entityManager->persist($client);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/register.html.twig', [
            'registerForm' => $registerForm->createView()
        ]);
    }
}
