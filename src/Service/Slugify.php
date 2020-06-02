<?php
namespace App\Service;

class Slugify
{
    public function removeSpecialCharacters($input) : string
    {
        $utf8 = [
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u' => 'A',
            '/[ÍÌÎÏ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/[«»]/u' => '',
        ];
        return preg_replace(array_keys($utf8), array_values($utf8), $input);
    }

    public function generate(string $input) : string
    {
        return trim(strtolower(preg_replace(['#[^A-Za-z0-9 -]+#', '#[\s-]+#'], ['', '-'], $this->removeSpecialCharacters($input))));
    }
}
