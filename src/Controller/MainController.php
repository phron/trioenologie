<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Occasion;
use App\Form\ContactType;
use App\Services\MailerService;
use App\Repository\ArticleRepository;
use App\Repository\GalleryRepository;
use App\Repository\CarouselRepository;
use App\Repository\OccasionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    // Route pour la page principale
    #[Route('/', name: 'home')]
    public function index(CarouselRepository $carouselRepository): Response
    {
        $carousels = $carouselRepository->findAll();
        return $this->render("main/home.html.twig", [
            "carousels" => $carousels
        ]);
    }

    // Route pour afficher la page actualités (navbar) on affiche tous les articles
    #[Route('/actus', name:'actus')]
    public function actualites(ArticleRepository $repoArticle)
    {
        $articles = $repoArticle->findAll();
        return $this->render('main/pages/actus.html.twig', [
            "articles" => $articles
        ]);
    }

    // Route pour afficher la page actualités (navbar) on affiche tous les articles
    #[Route('/asso', name:'asso')]
    public function asso()
    {
        
        return $this->render('main/pages/asso.html.twig');
    }

    // Route pour afficher tous les évènements de l'entity Occasion
    #[Route('/evenements', name: 'events')]
    public function indexOccasions(OccasionRepository $occasionRepository)
    {
        $occasions = $occasionRepository->findAll();
        return $this->render('main/pages/programme/events.html.twig', [
            'occasions' => $occasions
        ]);
    }

    // Route pour afficher le calendrier qui contient tous les évènements(Occasions)
    #[Route('/calendrier', 'calendrier', methods: ['GET'])]
    public function calendrierUser(): Response
    {
        return $this->render('main/pages/programme/calendrierUser.html.twig');
    }


    // Route pour afficher les détails d'un évènement(Occasion) en récupérant son ID
    #[Route('/calendrier/{id}', name: 'showEvent', methods: ['GET'])]
    public function showEvent(Occasion $occasion): Response
    {
        return $this->render('main/pages/programme/showEvent.html.twig', [
            'occasion' => $occasion,
        ]);
    }

    // Route pour afficher la galerie photo
    #[Route('/galerie', name:'galerie')]
    public function galerie(GalleryRepository $galleryRepository)
    {
        $galleries = $galleryRepository->findAll();
        return $this->render('main/pages/galerie/galerie.html.twig', [
            "galleries" => $galleries
        ]);
    }

    // Route pour afficher le détail d'une image dans la galerie photo
    #[Route('/galerie/{id}', name:'details_galerie', methods: ['GET'])]
    public function showDetailsGallery(Gallery $gallery)
    {
        return $this->render('main/pages/galerie/showDetailsGallery.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    // Route vers le formulaire de contact
    #[Route('/contact', name:'contact')]
    public function contact(
        Request $request,
        MailerService $mailer
    )
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $subject = 'Demande de contact sur votre site de ' . $contactFormData['email'];
            $content = $contactFormData['email'] . ' vous a envoyé le message suivant: ' . $contactFormData['message'];
            $mailer->sendEmail(subject: $subject, content: $content);
            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('home');
        }
        return $this->render('main/pages/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Route pour les mentions légales du site 
    #[Route('/mentions_legales', name:'mentions')]
    public function mentionsLegales()
    {
        return $this->render("main/partials/footer/mentions_legales.html.twig");
    }
}

