<?php

namespace App\Controller;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class TacheController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @param TacheRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/tache', name: 'app_tache', methods: ['GET'])]
    public function index(TacheRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $tache = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            7 
        );
        return $this->render('pages/tache/index.html.twig', [
            'taches' => $tache
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/tache/nouvelle', name: 'tache.index', methods: ['GET', 'POST'])]

    public function new(
        Request $request,
        EntityManagerInterface $manager
    ) : Response
    {
        $tache = new tache();
        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $tache = $form->getData();

            $manager->persist($tache);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre tache a été créé avec succès !'
            );

            return $this->redirectToRoute('app_tache');
        }

        return $this->render('pages/tache/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/tache/edition/{id}', 'tache.edit', methods: ['GET', 'POST'])]
    public function edit(TacheRepository $repository, int $id, Request $request, EntityManagerInterface $manager) : Response 
    {
        $tache = $repository->findOneBy(["id" => $id]);
        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $tache = $form->getData();

            $manager->persist($tache);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre tache a été modifié avec succès !'
            );

            return $this->redirectToRoute('app_tache');
        }
        return $this->render('pages/tache/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tache/suppression/{id}', 'tache.delete', methods: ['GET'])]
public function delete(TacheRepository $repository, EntityManagerInterface $manager, int $id) : Response 
{
    $tache = $repository->findOneBy(["id" => $id]);
    $manager->remove($tache);
    $manager->flush();

    $this->addFlash(
        'success',
        'Votre tache a été supprimé avec succès !'
    );

    return $this->redirectToRoute('app_tache');
}

    
}

