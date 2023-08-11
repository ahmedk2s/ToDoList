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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\VarDumper\VarDumper;


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
        $taches = $repository->findBy([], ['date_echeance' => 'ASC']);;

        $statut = $request->query->get('statut');
        $sort = $request->query->get('sort');
        $page = $request->query->getInt('page', 1);

        // Récupérer toutes les tâches de l'utilisateur
        $toutesTaches = $repository->findBy(['user' => $this->getUser()]);

        // Récupération des tâches depuis le repository avec tri et filtre
        $queryBuilder = $repository->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $this->getUser());

        if ($statut !== null) {
            $queryBuilder->andWhere('t.statut = :statut')
                ->setParameter('statut', $statut);
        }

    

        // Pagination
        $tache = $paginator->paginate(
            $queryBuilder->getQuery(),
            $page,
            4 // Nombre d'éléments par page
        );

    // Comptez le nombre de tâches terminées, en attente et en cours
    $tachesTerminees = array_filter($toutesTaches, function ($tache) {
        return $tache->getStatut() === 'Terminée';
    });

    $tachesEnAttente = array_filter($toutesTaches, function ($tache) {
        return $tache->getStatut() === 'À faire';
    });

    $tachesEnCours = array_filter($toutesTaches, function ($tache) {
        return $tache->getStatut() === 'En cours';
    });

    return $this->render('pages/tache/index.html.twig', [
        'taches' => $tache,
        'totalTachesTerminees' => count($tachesTerminees),
        'totalTachesEnAttente' => count($tachesEnAttente),
        'totalTachesEnCours' => count($tachesEnCours),
    ]);
    
}

    
    #[Route('/{id}', name: 'tache.show', methods: ['GET'])]
    public function show(int $id, TacheRepository $tacheRepository): Response
    {
        $tache = $tacheRepository->find($id);
    
        if (!$tache) {
            throw $this->createNotFoundException('Tache introuvable.');
        }
    
        return $this->render('pages/tache/show.html.twig', [
            'tache' => $tache,
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
        // Vérification de l'autorisation manuellement
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Accès refusé.');
        }
        $tache = new tache();
        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $tache = $form->getData();
            $tache->setUser($this->getUser());
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

    #[Route('/tache/edition/{id}', name: 'tache.edit', methods: ['GET', 'POST'])]
    public function edit(TacheRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $tache = $repository->findOneBy(['id' => $id]);

        // Vérification de l'autorisation manuellement
        if (!$this->isGranted('ROLE_USER') || $this->getUser() !== $tache->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier cette tâche.');
        }

        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'Votre tache a été modifiée avec succès !');

            return $this->redirectToRoute('app_tache');
        }

        return $this->render('pages/tache/edit.html.twig', [
            'form' => $form->createView(),
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

