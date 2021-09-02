<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Personne;
use App\Form\EquipeType;
use App\Form\PersonneType;
use App\Repository\EquipeRepository;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamWorkController extends AbstractController
{
    /**
     * @Route("/", name="team_work")
     */
    public function index(Request $request,EntityManagerInterface $em, EquipeRepository $equipeRepository, PersonneRepository $personneRepository): Response
    {
        $equipeEntity = new Equipe;
        $personneEntity = new Personne;

        $formEquipe = $this->createForm(EquipeType::class,$equipeEntity);                
        $formPersonne = $this->createForm(PersonneType::class, $personneEntity);
        
        $equipes  = $equipeRepository->findall();
        $personnes = $personneRepository->findAll();

        return $this->render('/index.html.twig', [
            'equipes' =>  $equipes,
            'personnes' => $personnes,

            'FormEquipe' => $formEquipe->createView(),
            'Formpersonne' => $formPersonne->createView(),
        ]);
    }

        /**
     * @Route("/ajoutEquipe", name="ajout_equipe")
     */
    public function ajout_equipe(EquipeRepository $equipeRepository,Request $request, EntityManagerInterface $em): Response
    {
        $equipeEntity = new Equipe;
        //dd($request);
        $equipeEntity->setNom($request->get('equipe')['nom']);
        $em->persist($equipeEntity);
        $em->flush();
        return $this->redirectToRoute('team_work');
    }

        /**
     * @Route("/AjoutPersonne", name="ajout_personne")
     */
    public function ajout_personne(EntityManagerInterface $em, EquipeRepository $equipeRepository,Request $request): Response
    {
        $personneEntity = new Personne;

        $formPersonne = $this->createForm(PersonneType::class, $personneEntity);
        
        $formPersonne->handleRequest($request);
        $equipe = $formPersonne->get('equipes')->getData();
        $personneEntity->addEquipe($equipe);
        $em->persist($personneEntity);
        $em->flush();
        
        return $this->redirectToRoute('team_work');

    }
        /**
     * @Route("/suprequipe/{equipe}", name="equipe_enlever")
     */
    public function equipe_enlever(EntityManagerInterface $em, Equipe $equipe): Response
    {
        $em->remove($equipe);
        $em->flush();
        
        return $this->redirectToRoute('team_work');

    }
        /**
     * @Route("/suprPerso/{personne}", name="personne_enlever")
     */
    public function personne_enlever(EntityManagerInterface $em, Personne $personne): Response
    {
        $em->remove($personne);
        $em->flush();
        
        return $this->redirectToRoute('team_work');

    }
        /**
     * @Route("/suprPersoEquipe/{personne}/{equipe}", name="personne_equipe_enlever")
     */
    public function personne_equipe_enlever(EntityManagerInterface $em, Personne $personne,Equipe $equipe): Response
    {
        $equipe->removePersonne($personne);
        $em->flush();
        
        return $this->redirectToRoute('team_work');

    }




}
