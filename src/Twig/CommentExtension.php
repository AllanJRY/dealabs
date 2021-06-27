<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommentExtension extends AbstractExtension
{
    /**
     * @var CategoryRepository
     */
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_nb_all_comment', [$this, 'getNbAllCommentaire']),
        ];
    }

    public function getNbAllCommentaire(): int
    {
        return count($this->commentRepository->findAll());
    }
}
