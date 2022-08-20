<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Gallery;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/gallery', name:'admin_gallery_')]
class AdminGalleryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexGallery(GalleryRepository $galleryRepository): Response
    {
        return $this->render('admin/gallery/gallery.html.twig', [
            'galleries' => $galleryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('admin_gallery_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/gallery/newGallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showGallery(Gallery $gallery): Response
    {
        return $this->render('admin/gallery/showGallery.html.twig', [
            'gallery' => $gallery,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('admin_gallery_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/gallery/editGallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deleteGallery(
        Request $request, 
        Gallery $gallery, 
        GalleryRepository $galleryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gallery->getId(), $request->request->get('_token'))) {
            $galleryRepository->remove($gallery, true);
        }

        return $this->redirectToRoute('admin_gallery_index', [], Response::HTTP_SEE_OTHER);
    }
}
