<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Gallery;
use App\Entity\Picture;
use App\Entity\Carousel;
use App\Form\ArticleType;
use App\Form\GalleryType;
use App\Form\PictureType;
use App\Form\CarouselType;
use App\Repository\ArticleRepository;
use App\Repository\GalleryRepository;
use App\Repository\PictureRepository;
use App\Repository\ProfileRepository;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// ROUTES UTILISATEUR ROLE EDITEUR

#[Route('/editor', name: 'editor_')]
class EditorController extends AbstractController
{

    // PROFIL UTILISATEUR

    #[Route('/profile', name: 'profile')]
    public function show(ProfileRepository $profileRepository): Response
    {
        return $this->render('user/profile/showProfile.html.twig', [
            'profile' => $profileRepository->findOneByUserId($this->getUser()),
        ]);
    }

    // DASHBOARD
    
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardEditor(): Response
    {
        return $this->render('editor/dashboard.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

    // ARTICLES

    #[Route('/articles', name: 'articles', methods: ['GET'])]
    public function indexArticles(ArticleRepository $articleRepository): Response
    {
        return $this->render('editor/article/articles.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/new', name: 'article_new', methods: ['GET', 'POST'])]
    public function newArticle(Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTimeImmutable('now'));         
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
                $article -> addPicture($pict);               
            } // EO foreach $pictures

            // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
            $images =  $form->get('savedPictures')->getData();
            
            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
                // on ajoute chaque image sélectionnée à l'article
                $article -> addPicture($image);
            }//EO foreach $images

            // On enregistre l'article 
            // qui va sauvegarder définitivement en bdd les images uploadées et créer les liens dans la table de jointure
            // grâce au 'cascade:['persist] ajouté dans la déclaration de la relation (cf Entity/Article))
            $entityManager->persist($article);
            $entityManager->flush();
            $articleRepository->add($article, true);


            return $this->redirectToRoute('editor_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/article/newArticle.html.twig', [
            'article' => $article,
            'formArticle' => $form,
        ]);
    }

    #[Route('/article/{id}', name: 'article_show', methods: ['GET'])]
    public function showArticle(Article $article): Response
    {
        return $this->render('editor/article/showArticle.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{id}/edit', name: 'article_edit', methods: ['GET', 'POST'])]
    public function editArticle(Request $request, Article $article, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
        $images =  $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
            // on ajoute chaque image sélectionnée à l'article
            $article -> addPicture($image);
        }
            $articleRepository->add($article, true);

            return $this->redirectToRoute('editor_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/article/editArticle.html.twig', [
            'article' => $article,
            'formArticle' => $form,
        ]);
    }

    #[Route('/article/{id}', name: 'article_delete', methods: ['POST'])]
    public function deleteArticle(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('editor_articles', [], Response::HTTP_SEE_OTHER);
    }

    // GALERIE PHOTO

    #[Route('/gallery', name: 'gallery', methods: ['GET'])]
    public function indexGallery(GalleryRepository $galleryRepository): Response
    {
        return $this->render('editor/gallery/gallery.html.twig', [
            'galleries' => $galleryRepository->findAll(),
        ]);
    }

    #[Route('/gallery/new', name: 'gallery_new', methods: ['GET', 'POST'])]
    public function newGallery(
    Request $request,
    GalleryRepository $galleryRepository,
    EntityManagerInterface $entityManager): Response
    {
        $gallery = new Gallery();
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $gallery->setCreatedAt(new DateTimeImmutable('now'));     
            $pictures = $form->get('pictures')->getData();
            
            foreach($pictures as $picture){
                
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $picture->getClientOriginalExtension();
                               
                $name = $picture->getClientOriginalName();
                
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $nomPict
                ); 
                
                $pict = new Picture();
                $pict->setTitle($name); 
                $pict->setPictureFile($nomPict);
                
                $gallery -> addPicture($pict);               
            }
            
            $images =  $form->get('savedPictures')->getData();
            
            foreach($images as $image){
                
                $gallery -> addPicture($image);
            }

            $entityManager->persist($gallery);
            $entityManager->flush();
            $galleryRepository->add($gallery, true);

            return $this->redirectToRoute('editor_gallery', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/gallery/newGallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/gallery/{id}', name: 'gallery_show', methods: ['GET'])]
    public function showGallery(Gallery $gallery): Response
    {
        return $this->render('editor/gallery/showGallery.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'gallery_edit', methods: ['GET', 'POST'])]
    public function editGallery(
    Request $request, 
    Gallery $gallery, 
    GalleryRepository $galleryRepository,
    EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);
            
        // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
        $images =  $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
            // on ajoute chaque image sélectionnée à la galerie photo
            $gallery -> addPicture($image);
        }    

            $entityManager->persist($gallery);
            $entityManager->flush();
            $galleryRepository->add($gallery, true);

            return $this->redirectToRoute('editor_gallery', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/gallery/editGallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/gallery/{id}', name: 'gallery_delete', methods: ['POST'])]
    public function deleteGallery(Request $request, Gallery $gallery, GalleryRepository $galleryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->request->get('_token'))) {
            $galleryRepository->remove($gallery, true);
        }

        return $this->redirectToRoute('editor_gallery', [], Response::HTTP_SEE_OTHER);
    }

    // CAROUSEL


    #[Route('/carousel', name: 'carousel', methods: ['GET'])]
    public function indexCarousel(CarouselRepository $carouselRepository): Response
    {
        return $this->render('editor/carousel/carousel.html.twig', [
            'carousels' => $carouselRepository->findAll(),
        ]);
    }

    #[Route('/carousel/new', name: 'carousel_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        CarouselRepository $carouselRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $carousel = new Carousel();
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            $pictures = $form->get('pictures')->getData();
            
            foreach($pictures as $picture){
                
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $picture->getClientOriginalExtension();
                               
                $name = $picture->getClientOriginalName();
                
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $nomPict
                ); 
                
                $pict = new Picture();
                $pict->setTitle($name); 
                $pict->setPictureFile($nomPict);
                
                $carousel -> addPicture($pict);               
            }
            
            $images =  $form->get('savedPictures')->getData();
            
            foreach($images as $image){
                
                $carousel -> addPicture($image);
            }

            $entityManager->persist($carousel);
            $entityManager->flush();
            $carouselRepository->add($carousel, true);

            return $this->redirectToRoute('editor_carousel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/carousel/newCarousel.html.twig', [
            'carousel' => $carousel,
            'form' => $form,
        ]);
    }

    #[Route('/carousel/{id}', name: 'carousel_show', methods: ['GET'])]
    public function showCarousel(Carousel $carousel): Response
    {
        return $this->render('editor/carousel/showCarousel.html.twig', [
            'carousel' => $carousel,
        ]);
    }

    #[Route('/carousel/{id}/edit', name: 'carousel_edit', methods: ['GET', 'POST'])]
    public function editCarousel(
        Request $request, 
        Carousel $carousel, 
        CarouselRepository $carouselRepository, 
        EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);
        $images =  $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            foreach($images as $image){
            $carousel -> addPicture($image);
        }

            $entityManager->persist($carousel);
            $entityManager->flush();
            $carouselRepository->add($carousel, true);

            return $this->redirectToRoute('editor_carousel', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/carousel/editCarousel.html.twig', [
            'carousel' => $carousel,
            'form' => $form,
        ]);
    }

    #[Route('/carousel/{id}', name: 'carousel_delete', methods: ['POST'])]
    public function deleteCarousel(
        Request $request, 
        Carousel $carousel, 
        CarouselRepository $carouselRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carousel->getId(), $request->request->get('_token'))) {
            $carouselRepository->remove($carousel, true);
        }

        return $this->redirectToRoute('editor_carousel', [], Response::HTTP_SEE_OTHER);
    }

    // IMAGES

    #[Route('/pictures', name: 'pictures', methods: ['GET'])]
    public function indexPicture(PictureRepository $pictureRepository): Response
    {
        return $this->render('editor/picture/pictures.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/picture/new', name: 'picture_new', methods: ['GET', 'POST'])]
    public function newPicture(
        Request $request, 
        PictureRepository $pictureRepository,
        EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture, ['add'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère la valeur du champ d'upload (pictureFile)
            $pictures = $form->get('pictureFile')->getData();          
            $message = "";
            $dup=[];

            // pour chacque image ($pict) dans le tableau $pictures
            foreach($pictures as $pict){                                    
                $picture = new Picture();
                // on attribue un nom de fichier unique à l'image téléchargée
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $pict->getClientOriginalExtension();                
                // on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
                // affecte le nom de fichier calculé à la propriété 'pictureFile' de l'entité Picture
                $picture->setPictureFile($nomPict);
                //on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
        
                // on vérifie qu'une image avec ce nom n'existe pas déjà  
                // s'il existe une image avec le même titre dans la bdd
                if ($pictureRepository->findOneByTitle($name)) {
                    // on stoppe le traitement pour cette image et on repart en haut de la boucle pour l'itération suivante
                    continue;
                }else {
                    // on l'affecte à la propriété title
                    $picture->setTitle($name);                    
                    // on enregistre en bdd (les infos de l'image)
                    $entityManager->persist($picture);
                    $entityManager->flush();
                    
                    // on enregistre l'image dans le répertoire uploads/pictures (image physique)
                    $pict->move(
                        $this->getParameter('pictures_directory'),
                        $nomPict
                    ); // EO move     
                    
                    $pictureRepository->add($picture, true);
                }                    
            }
           
            return $this->redirectToRoute('editor_pictures', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('editor/picture/newPicture.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/picture/{id}', name: 'picture_show', methods: ['GET'])]
    public function showPicture(Picture $picture): Response
    {
        return $this->render('editor/picture/showPicture.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/picture/{id}/edit', name: 'picture_edit', methods: ['GET', 'POST'])]
    public function editPicture(
        Request $request, 
        Picture $picture, 
        PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class, $picture, ['edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureRepository->add($picture, true);

            return $this->redirectToRoute('editor_pictures', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/picture/editPicture.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    //L'éditeur ne possède pas le droit de supprimer une image de la banque d'images
}
