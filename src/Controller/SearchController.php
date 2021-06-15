<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Deal;
use App\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * SearchController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }


    /**
     * @Route({"en": "/search", "fr": "/recherche"}, name="search")
     */
    public function index(Request $request): Response
    {
        $query = $request->query->get('s');

        $dealRepo = $this->entityManager->getRepository(Deal::class);
        $dealsFound = $dealRepo->findWhichContains($query, 10);

        $categRepo = $this->entityManager->getRepository(Category::class);
        $categsFound = $categRepo->findAllWhichContains($query);

        $partnerRepo = $this->entityManager->getRepository(Partner::class);
        $partnersFound = $partnerRepo->findAllWhichContains($query);

        return $this->render('pages/search/index.html.twig', [
            'query' => $query,
            'deals_found' => $dealsFound,
            'categs_found' => $categsFound,
            'partners_found' => $partnersFound,
        ]);
    }

    /**
     * @Route({"en": "/search/deals", "fr": "/recherche/deals"}, name="search_deals")
     */
    public function searchDeals(Request $request): Response
    {
        $query = $request->query->get('s');

        $dealRepo = $this->entityManager->getRepository(Deal::class);
        $dealsFound = $this->paginator->paginate(
            $dealRepo->findAllWhichContains($query),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/search/deals.html.twig', [
            'deals_found' => $dealsFound,
        ]);
    }
    // TODO: add route for individual entity search ? for 'show more' links, gives the possibility to add pagination and be cleaner
}
