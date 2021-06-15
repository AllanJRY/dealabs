<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Deal;
use App\Entity\Partner;
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

        $categRepo = $this->entityManager->getRepository(Category::class);
        $categsFound = $categRepo->findAllWhichContains($query);

        $partnerRepo = $this->entityManager->getRepository(Partner::class);
        $partnersFound = $partnerRepo->findAllWhichContains($query);

        return $this->render('pages/search/index.html.twig', [
            'deals_found' => $dealsFound,
            'categs_found' => $categsFound,
            'partners_found' => $partnersFound,
        ]);
    }

    // TODO: add route for individual entity search ? for 'show more' links, gives the possibility to add pagination and be cleaner
}
