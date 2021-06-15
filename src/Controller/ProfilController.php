<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route({
 *     "en": "/profile",
 *     "fr": "/profile"
 * })
 */
class ProfilController extends AbstractController
{

    /**
     * @Route("/overview", name="overview")
     */
    public function overview(): Response
    {
        return $this->render('pages/profil/overview.html.twig', []);
    }

    /**
     * @Route("/badges", name="badges")
     */
    public function badges(): Response
    {
        return $this->render('pages/profil/badges.html.twig', []);
    }

    /**
     * @Route("/good_plan", name="good_plan")
     */
    public function good_plan(): Response
    {
        return $this->render('pages/profil/good_plan.html.twig', []);
    }

    /**
     * @Route("/discussions", name="discussions")
     */
    public function discussions(): Response
    {
        return $this->render('pages/profil/discussions.html.twig', []);
    }

    /**
     * @Route("/saved_deals", name="saved_deals")
     */
    public function saved_deals(): Response
    {
        return $this->render('pages/profil/saved_deals.html.twig', []);
    }

    /**
     * @Route("/keyword_alarms", name="keyword_alarms")
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

}
