<?php

namespace App\Controller;
use App\Repository\TacheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;

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
}

