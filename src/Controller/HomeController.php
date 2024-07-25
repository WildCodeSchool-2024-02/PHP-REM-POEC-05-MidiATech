<?php

namespace App\Controller;

use App\Model\BooksManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     */
    public function index(): string
    {
        $booksManager = new BooksManager();
        $musicsManager = new MusicsManager();
        $videosManager = new VideosManager();

        $bookImg = $booksManager->selectImgMostRecent();
        $musicImg = $musicsManager->selectImgMostRecent();
        $videoImg = $videosManager->selectImgMostRecent();

        $carrouselImg = [$bookImg, $musicImg, $videoImg];
        $mediaType = ['books', 'musics', 'videos'];
        return $this->twig->render('Home/index.html.twig', ['carrouselImg' => $carrouselImg,
        'media_type' => $mediaType]);
    }

    public function search(): string|false
    {
        $searchTerm = $_GET['term'] ?? '';
        $results = [];

        // Musics
        $musicsManager = new MusicsManager();
        $musics = $musicsManager->search($searchTerm);
        if ($musics) {
            $results['musics'] = $musics;
        }

        // Books
        $booksManager = new BooksManager();
        $books = $booksManager->search($searchTerm);
        if ($books) {
            $results['books'] = $books;
        }

        // Videos
        $videosManager = new VideosManager();
        $videos = $videosManager->search($searchTerm);
        if ($videos) {
            $results['videos'] = $videos;
        }

        header('Content-Type: application/json');

        echo json_encode($results, JSON_THROW_ON_ERROR);
        exit;
    }
}
