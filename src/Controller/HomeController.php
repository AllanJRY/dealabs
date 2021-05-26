<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DealRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var DealRepository
     */
    private $dealRepository;

    public function __construct(CategoryRepository $categoryRepository, DealRepository $dealRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->dealRepository = $dealRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $categories = $this->categoryRepository->findAll();
        $data = $this->dealRepository->findAllOrderByCreatedAtDesc();

        $deals = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/home/index.html.twig', [
            'deals' => $deals,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/hot", name="hot_home")
     */
    public function hot(Request $request, PaginatorInterface $paginator): Response
    {
        $categories = $this->categoryRepository->findAll();
        $data = $this->dealRepository->findAllHotOrderByRatingDesc();

        $deals = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/home/index.html.twig', [
            'categories' => $categories,
            'deals' => $deals,
        ]);
    }
}
