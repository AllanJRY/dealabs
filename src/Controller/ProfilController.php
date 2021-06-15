<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfilController
 * @package App\Controller
 * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
 * @Route({
 *     "en": "/my-profil",
 *     "fr": "/mon-profil"
 * })
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/", name="profil_index")
     */
    public function index(): Response
    {
        return $this->render('pages/profil/index.html.twig', [
        ]);
    }
}
