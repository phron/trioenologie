<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Profile;
use App\Repository\UserRepository;
use App\Form\Admin\AdminProfileType;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/profile', name:'admin_profile_')]
class AdminProfileController extends AbstractController
{
    // #[Route('/', name: 'index', methods: ['GET'])]
    // public function indexProfileUser(ProfileRepository $profileRepository): Response
    // {
    //     return $this->render('admin/user/profile/profiles.html.twig', [
    //         'profiles' => $profileRepository->findAll(),
    //     ]);
    // }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showProfile(Profile $profile): Response
    {
        return $this->render('admin/user/profile/showProfile.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editProfile(
        Request $request, 
        Profile $profile, 
        ProfileRepository $profileRepository): Response
    {
        $form = $this->createForm(AdminProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setUpdatedAt(new DateTimeImmutable('now'));         
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/profile/editProfile.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function deleteProfile(
        Request $request, 
        Profile $profile, 
        ProfileRepository $profileRepository, 
        UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $user = $profile->getUser(); // On récupère l'utilisateur par le biais de son profil
            $profileRepository->remove($profile, true); // On supprime son profil
            $userRepository->remove($user, true); // On supprime l'utilisateur
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

}
