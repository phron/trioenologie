<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Gallery;
use App\Entity\Carousel;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\GalleryRepository;
use App\Repository\ProfileRepository;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/editor', name: 'editor_')]
class EditorController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function show(ProfileRepository $profileRepository): Response
    {
        return $this->render('user/profile/showProfile.html.twig', [
            'profile' => $profileRepository->findOneByUserId($this->getUser()),
        ]);
    }
    
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardEditor(): Response
    {
        return $this->render('editor/dashboard.html.twig', [
            'controller_name' => 'EditorController',
        ]);
    }

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
        $form = $this->createForm(ArticleType::class, $article, ['add' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new DateTimeImmutable('now'));         
            $imgArticleFile = $form->get('img')->getData();


            if($imgArticleFile)
            {
                $nomImgArticle = date('YmdHis') . "-" . uniqid() . "." . $imgArticleFile->getClientOriginalExtension();

                $imgArticleFile->move(
                    $this->getParameter('articles_directory'),
                    $nomImgArticle
                );

                $article->setImg($nomImgArticle);
            }

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
        $form = $this->createForm(ArticleType::class, $article, ['update' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgArticleFile = $form->get('imgUpdate')->getData();


            if($imgArticleFile)
            {
                $nomImgArticle = date('YmdHis') . "-" . uniqid() . "." . $imgArticleFile->getClientOriginalExtension();

                $imgArticleFile->move(
                    $this->getParameter('articles_directory'),
                    $nomImgArticle
                );

                $article->setImg($nomImgArticle);
            }

            $entityManager->persist($article);
            $entityManager->flush();
            $articleRepository->add($article, true);

            return $this->redirectToRoute('editor_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('editor/article/editArticle.html.twig', [
            'article' => $article,
            'form' => $form,
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
        $form = $this->createForm(GalleryType::class, $gallery, ['add' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $gallery->setCreatedAt(new DateTimeImmutable('now'));         
            $imgGallery = $form->get('img')->getData();


            if($imgGallery)
            {
                $nomImgGallery = date('YmdHis') . "-" . uniqid() . "." . $imgGallery->getClientOriginalExtension();

                $imgGallery->move(
                    $this->getParameter('gallery_directory'),
                    $nomImgGallery              );

                $gallery->setImg($nomImgGallery);
            }

            $entityManager->persist($gallery);
            $entityManager->flush();
            $galleryRepository->add($gallery, true);
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
        $form = $this->createForm(GalleryType::class, $gallery, ['update' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $gallery->setCreatedAt(new DateTimeImmutable('now'));         
            $imgGallery = $form->get('imgUpdate')->getData();


            if($imgGallery)
            {
                $nomImgGallery = date('YmdHis') . "-" . uniqid() . "." . $imgGallery->getClientOriginalExtension();

                $imgGallery->move(
                    $this->getParameter('gallery_directory'),
                    $nomImgGallery              );

                $gallery->setImg($nomImgGallery);
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
        $form = $this->createForm(CarouselType::class, $carousel, ['add' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            $imgCarouselFile = $form->get('img')->getData();


            if($imgCarouselFile)
            {
                $nomImgCarousel = date('YmdHis') . "-" . uniqid() . "." . $imgCarouselFile->getClientOriginalExtension();

                $imgCarouselFile->move(
                    $this->getParameter('carousel_directory'),
                    $nomImgCarousel
                );

                $carousel->setImg($nomImgCarousel);
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
        $form = $this->createForm(CarouselType::class, $carousel, ['update' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgCarouselFile = $form->get('imgUpdate')->getData();


            if($imgCarouselFile)
            {
                $nomImgCarousel= date('YmdHis') . "-" . uniqid() . "." . $imgCarouselFile->getClientOriginalExtension();

                $imgCarouselFile->move(
                    $this->getParameter('carousel_directory'),
                    $nomImgCarousel                );

                $carousel->setImg($nomImgCarousel);
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
}
