<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileExtension extends AbstractExtension
{

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('file_exists', [$this, 'FileExists']),
        ];
    }

    /**
     * @param $file
     * @return bool
     */
    public function FileExists($file): bool
    {
        return file_exists($file);
    }
}
