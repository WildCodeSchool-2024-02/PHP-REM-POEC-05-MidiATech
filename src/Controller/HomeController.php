<?php

namespace App\Controller;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): string
    {

        $book = $this->managers->booksManager->selectMostRecent();
        $book['mediaType'] = 'books';
        $music = $this->managers->musicsManager->selectMostRecent();
        $music['mediaType'] = 'musics';
        $video = $this->managers->videosManager->selectMostRecent();
        $video['mediaType'] = 'videos';

        $medias = [$book, $music, $video];
        return $this->twig->render('Home/index.html.twig', ['medias' => $medias]);
    }
}
