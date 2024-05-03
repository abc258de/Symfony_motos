<?php

namespace App\Controller;

use App\Repository\MotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Moto;
use App\Form\MotorcyclesType;


class HomeController extends AbstractController 
{
 #[Route(path: "/", name: "home")]
     public function index(Request $request, motoRepository $repository, EntityManagerInterface $em): Response
     {
         $allbikes = $repository->findAll();

         // Retrieve the last 4 entries from the database
         $lastTwoBikes = $repository->findBy([], ['id' => 'DESC'], 2);
 
         return $this->render('home/index.html.twig', [
             'bikesTable' => $allbikes,
             'bikesNew' => $lastTwoBikes, 
         ]);
     }

//  TEMPLATE FOR THE SHOW

#[Route(path: '/{nom}-{id}', name: 'show', requirements : ['id'=> '\d+', 'nom'=> '[a-zA-Z0-9-]+'])]
     public function show(Request $request, string $nom, int $id, motoRepository $repository ) : Response
     {
         $bikesid = $repository->find($id);
                         
         if($bikesid->getNom() !== $nom){
             return $this->redirectToRoute('show', ['id' => $bikesid->getId(), 'nom' => $bikesid->getNom()]);
         }
 
         return $this->render('home/show.html.twig', [ 'bikeSolo' => $bikesid
         ]);
 
     }    
     
//  TEMPLATE TO EDIT

#[Route(path : '/edit/{id}', name : 'form_edit')]
public function edit(Moto $motos, Request $request, EntityManagerInterface $em) : Response{ 
    $form = $this->createForm(MotorcyclesType::class, $motos);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
        $em->flush();
     // $this->addFlash('success', 'The bike ' . $motos->getModel() . 'has been edited');
     
        return $this->redirectToRoute('home', ['id' => $motos->getId(), 'nom' => $motos->getNom()]);
    }

    return $this->render('home/edit.html.twig',[
        'motoSolo' => $motos,
        'monForm' => $form
    ]);
}

//  TEMPLATE TO CREATE

#[Route(path : '/create', name : 'form_create')]
public function create(Request $request, EntityManagerInterface $em) : Response{
    $motos=new Moto();
    $form = $this->createForm(MotorcyclesType::class, $motos);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
        $em->persist($motos);
        $em->flush();
        // $this->addFlash('success', 'The bike ' . $motos->getModel() . 'has been created');
        return $this->redirectToRoute('home', ['id' => $motos->getId()]);
    }
    return $this->render('home/create.html.twig',[
        // une methode, une page
       'form_create' => $form
    ]);
}

// TEMPLATE TO DELETE

#[Route(path : '/delete/{id}', name : 'form_delete')]
public function delete(Moto $motos, EntityManagerInterface $em) : Response{
   $titre=$motos ->getNom();
   $em->remove($motos);
   $em->flush();
//    $this->addFlash('info', 'The Model' . $titre . 'has been removed from the database');
   return $this->redirectToRoute('home');
   }

      
}
 

 

