<?php

namespace App\Controller\Admin;

use App\Entity\Carousel;
use App\Form\CarouselType;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

            return $this->redirectToRoute('admin_carousel', [], Response::HTTP_SEE_OTHER);
        }

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
