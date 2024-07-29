<?php

namespace App\Controller;

class SearchController extends AbstractController
{
    public function search(): void
    {
        $searchTerm = $_GET['term'] ?? '';
        $results = [];

        // Musics
        $musics = $this->managers->musicsManager->search($searchTerm);
        if ($musics) {
            $results['musics'] = $musics;
        }

        // Books
        $books = $this->managers->booksManager->search($searchTerm);
        if ($books) {
            $results['books'] = $books;
        }

        // Videos
        $videos = $this->managers->videosManager->search($searchTerm);
        if ($videos) {
            $results['videos'] = $videos;
        }

        header('Content-Type: application/json');

        echo json_encode($results, JSON_THROW_ON_ERROR);
        exit;
    }
}
