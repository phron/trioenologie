<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user', name:'user_')]
class UserController extends AbstractController

{   
    #[Route('/profile', name: 'profile')]
    public function show(ProfileRepository $profileRepository): Response
    {
        return $this->render('user/profile/showProfile.html.twig', [
            'profile' => $profileRepository->findOneByUserId($this->getUser()),
        ]);
    }

    #[Route('/profile/{id}/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Profile $profile, 
        ProfileRepository $profileRepository): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setUpdatedAt(new DateTimeImmutable('now'));         
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/profile/editProfile.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    // On ne peut pas supprimer un utilisateur sans supprimer son profil et inversement.
    // On doit capturer la session de l'utilisateur puis la détruire afin d'éviter les erreurs a la redirection après la suppression du compte car un utilisateur connecté ne peut supprimer son propre compte tant que sa session est active.
    // On utilise la fonction prédéfinie PHP $requestStack pour capturer la session utilisateur en cours
    private $requestStack;
    
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/profile/{id}', name: 'profile_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Profile $profile, 
        ProfileRepository $profileRepository, 
        UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            $user = $profile->getUser(); // On récupère l'utilisateur connecté
            $profileRepository->remove($profile, true); // On supprime son profil
            $userRepository->remove($user, true); // On supprime l'utilisateur
            $session = $this->requestStack->getSession(); // On récupère la session
            $session = new Session(); // On instancie une nouvelle session vide (sans utilisateur)
            $session->invalidate(); // On détruit la session précédente (session utilisateur)
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER); // On redirige vers la page d'accueil dans ce cas précis
    }


        // profile_reset : remet tous les champs du profil à vide sauf user et updatedAt    
        #[Route('/profile/{id}', name: 'profile_reset', methods: ['GET','POST'])]
        public function reset(
            Profile $profile, 
            ProfileRepository $profileRepository): Response
        {
            
            $profile->setLastName("");
            $profile->setFirstName("");
            $profile->setPhoneNumber("");
            $profile->setAddress("");
            $profile->setAddress2("");
            $profile->setZipcode("");
            $profile->setCity("");
            $profile->setStatus(null);
            $profile->setUpdatedAt( new \DateTimeImmutable('now'));
            
            $profileRepository->add($profile, true);
    
    
            return $this->redirectToRoute('user_profile_edit', ['id' => $profile->getId()], Response::HTTP_SEE_OTHER);
            return $this->renderForm('user/profile/editProfile.html.twig', [
                'profile' => $profile,
            ]);
            
        }
}
