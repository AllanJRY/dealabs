<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        $deals = $this->dealRepository->findAll();

        return $this->render('pages/home/index.html.twig', [
            'categories' => $categories,
            'deals' => $deals,
        ]);
    }
}
