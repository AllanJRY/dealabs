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
     * @param PaginatorInterface $paginator
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
        $categsFound = $categRepo->findWhichContains($query, 10);

        $partnerRepo = $this->entityManager->getRepository(Partner::class);
        $partnersFound = $partnerRepo->findWhichContains($query, 10);

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
            'query' => $query,
            'deals_found' => $dealsFound,
        ]);
    }

    /**
     * @Route({"en": "/search/categories", "fr": "/recherche/categories"}, name="search_categs")
     */
    public function searchCategs(Request $request): Response
    {
        $query = $request->query->get('s');

        $categRepo = $this->entityManager->getRepository(Category::class);
        $categsFound = $this->paginator->paginate(
            $categRepo->findAllWhichContains($query),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/search/categories.html.twig', [
            'query' => $query,
            'categs_found' => $categsFound,
        ]);
    }

    /**
     * @Route({"en": "/search/partner", "fr": "/recherche/marchand"}, name="search_partners")
     */
    public function searchPartners(Request $request): Response
    {
        $query = $request->query->get('s');

        $partnerRepo = $this->entityManager->getRepository(Partner::class);
        $partnersFound = $this->paginator->paginate(
            $partnerRepo->findAllWhichContains($query),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/search/partners.html.twig', [
            'query' => $query,
            'partners_found' => $partnersFound,
        ]);
    }
}
