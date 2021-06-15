<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfilController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 * @Route({
 *     "en": "/profil",
 *     "fr": "/profile"
 * })
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/overview", name="profil_overview")
     */
    public function overview(): Response
    {
        return $this->render('pages/profil/overview.html.twig', []);
    }

    /**
     * @Route("/badges", name="profil_badges")
     */
    public function badges(): Response
    {
        return $this->render('pages/profil/badges.html.twig', []);
    }

    /**
     * @Route("/discussions", name="profil_discussions")
     */
    public function discussions(): Response
    {
        return $this->render('pages/profil/discussions.html.twig', []);
    }


    /**
     * @Route("/keyword_alarms", name="profil_keyword_alarms")
     */
    public function keyword_alarms(): Response
    {
        return $this->render('pages/profil/keyword_alarms.html.twig', []);
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function settings(): Response
    {
        return $this->render('pages/profil/settings.html.twig', []);
    }

    /**
     * @Route({"en": "/my-deals", "fr": "/mes-deals"}, name="profil_published_deals")
     */
    public function publishedDeals(Request $request, PaginatorInterface $paginator): Response
    {
        $userPublishedDeals = $paginator->paginate(
            $this->getUser()->getDeals(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/profil/published_deals.html.twig', [
            "user_published_deals" => $userPublishedDeals,
        ]);
    }

    /**
     * @Route({"en": "/my-saved-deals", "fr": "/mes-deals-sauvegarde"}, name="profil_saved_deals")
     */
    public function savedDeals(Request $request, PaginatorInterface $paginator): Response
    {
        $userSavedDeals = $paginator->paginate(
            $this->getUser()->getSavedDeals(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/profil/saved_deals.html.twig', [
            "user_saved_deals" => $userSavedDeals,
        ]);
    }
}
