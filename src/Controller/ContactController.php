<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Model\ContactMessage;
use App\Services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailService $mailService): Response
    {
        // ✅ On crée directement le DTO (Data Transfer Object / objet de transfert de données)
        $contactMessage = new ContactMessage();

        // ✅ On le lie au formulaire
        $form = $this->createForm(ContactType::class, $contactMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Plus besoin de transformation
            $mailService->sendContact($contactMessage);

            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route de test pour prévisualiser le rendu du mail de contact
    #[Route('/preview-html', name: 'preview_html')]
    public function previewHtml(Environment $twig)
    {
        return new Response(
            $twig->render('mails/contact.html.twig', [
                'name' => 'Jean',
                'userEmail' => 'jean@example.com',
                'subject' => 'Bonjour, je suis intéressé par vos produits. Pouvez-vous  me donner plus d\'informations ?',
                'message' => 'Merci d\'avance pour votre retour.',  
            ])
        );
    }
}