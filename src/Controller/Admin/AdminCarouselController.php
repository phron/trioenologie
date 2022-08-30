<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Carousel;
use App\Form\CarouselType;
use App\Repository\PictureRepository;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/carousel', name:'admin_')]
class AdminCarouselController extends AbstractController
{
    #[Route('/', name: 'carousel', methods: ['GET'])]
    public function indexCarousel(CarouselRepository $carouselRepository): Response
    {
        return $this->render('admin/carousel/carousel.html.twig', [
            'carousels' => $carouselRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'carousel_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        CarouselRepository $carouselRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $carousel = new Carousel();
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            // on récupère les images transmises dans le champ d'upload (pictures)
            $pictures = $form->get('pictures')->getData();
            
            // on boucle sur les images uploadées
            foreach($pictures as $picture){
                // on attribue un nom de fichier unique à l'image téléchargée
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $picture->getClientOriginalExtension();
                
                //on récupère le nom de fichier original de l'image
                $name = $picture->getClientOriginalName();
                
                // on enregistre l'image dans le répertoire uploads/pictures (image physique)
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $nomPict
                ); // EO move  

                // on enregistre l'image en BDD table Picture (ses infos)
                $pict = new Picture();
                $pict->setTitle($name); 
                $pict->setPictureFile($nomPict);

                // on enregistre l'image dans l'article
                $carousel -> addPicture($pict);               
            } // EO foreach $pictures

            // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
            $images =  $form->get('savedPictures')->getData();
            
            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
                // on ajoute chaque image sélectionnée à l'article
                $carousel -> addPicture($image);
            }//EO foreach $images

            // On enregistre l'article 
            // qui va sauvegarder définitivement en bdd les images uploadées et créer les liens dans la table de jointure
            // grâce au 'cascade:['persist] aj

            $entityManager->persist($carousel);
            $entityManager->flush();
            $carouselRepository->add($carousel, true);

            return $this->redirectToRoute('admin_carousel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/carousel/newCarousel.html.twig', [
            'carousel' => $carousel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'carousel_show', methods: ['GET'])]
    public function showCarousel(Carousel $carousel): Response
    {
        return $this->render('admin/carousel/showCarousel.html.twig', [
            'carousel' => $carousel,
        ]);
    }

    #[Route('/{id}/edit', name: 'carousel_edit', methods: ['GET', 'POST'])]
    public function editCarousel(
        Request $request, 
        Carousel $carousel, 
        CarouselRepository $carouselRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);
        // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
        $images =  $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
            // on ajoute chaque image sélectionnée à l'article
            $carousel -> addPicture($image);
        }

            $entityManager->persist($carousel);
            $entityManager->flush();
            $carouselRepository->add($carousel, true);

            return $this->redirectToRoute('admin_carousel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/carousel/editCarousel.html.twig', [
            'carousel' => $carousel,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{carousel_id}/unlinkPicture/{id}', name: 'carousel_unlinkPicture', methods: ['GET','DELETE'])]
    public function unlinkPicture(
        $carousel_id, 
        $id, 
        Request $request, 
        CarouselRepository $carouselRepository, 
        PictureRepository $pictureRepository, 
        EntityManagerInterface $entityManager ): Response
    {
        $carousel = $carouselRepository->find($carousel_id);
        $picture = $pictureRepository->find($id);
        $carousel->removePicture($picture);

        $entityManager->persist($carousel);
        $entityManager->flush();
       
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);
        
        return $this->renderForm('admin/carousel/editCarousel.html.twig', [
            'carousel' => $carousel,
            'form' => $form,
        ]);

    }



    #[Route('/{id}', name: 'carousel_delete', methods: ['POST'])]
    public function deleteCarousel(
        Request $request, 
        Carousel $carousel, 
        CarouselRepository $carouselRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carousel->getId(), $request->request->get('_token'))) {
            $carouselRepository->remove($carousel, true);
        }

        return $this->redirectToRoute('admin_carousel', [], Response::HTTP_SEE_OTHER);
    }
}
