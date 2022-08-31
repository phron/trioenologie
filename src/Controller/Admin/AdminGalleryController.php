<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Gallery;
use App\Entity\Picture;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use App\Repository\PictureRepository;
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

            return $this->redirectToRoute('admin_gallery_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/gallery/editGallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{gallery_id}/unlinkPicture/{id}', name: 'unlinkPicture', methods: ['GET','DELETE'])]
    public function unlinkPicture(
        $article_id, 
        $id, 
        Request $request, 
        GalleryRepository $galleryRepository, 
        PictureRepository $pictureRepository, 
        EntityManagerInterface $entityManager ): Response
    {
        $gallery = $galleryRepository->find($article_id);
        $picture = $pictureRepository->find($id);
        $gallery->removePicture($picture);

        $entityManager->persist($gallery);
        $entityManager->flush();
       
        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);
        
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
