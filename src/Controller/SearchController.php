<?php

namespace App\Controller;

use App\Entity\Deal;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SearchController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route({"en": "/search", "fr": "/recherche"}, name="search")
     */
    public function index(Request $request): Response
    {
        $query = $request->query->get('s');

        $dealRepo = $this->entityManager->getRepository(Deal::class);
        $dealsFound = $dealRepo->findAllWhichContains($query);

        return $this->render('pages/search/index.html.twig', [
            'deals_found' => $dealsFound,
        ]);
    }
}
