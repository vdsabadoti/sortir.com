<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class Censurator
{


    public function __construct(
        private readonly ContainerBagInterface $params
    )
    {

    }


    public function purify(?string $text): string
    {
        $filename = $this->params->get('censurator_file');
        if (file_exists($filename)) {
            $words = file($filename);
            foreach ($words as $unwantedWord) {
                $unwantedWord = str_replace(PHP_EOL, '', $unwantedWord);
                $replacement = str_repeat('*', mb_strlen($unwantedWord));
                $text = str_ireplace($unwantedWord, $replacement, $text);
            }
        }

        return $text;
    }

}