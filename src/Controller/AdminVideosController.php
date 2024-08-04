<?php

namespace App\Controller;

use App\Model\VideosManager;

class AdminvideosController extends AdminController
{
    public function categories(): string
    {
        $videosCategories = $this->managers->videosManager->selectCategories();
        foreach ($videosCategories as &$video) {
            $video['name'] = substr($video['name'], strlen(self::MEDIA_VIDEOS) + 1);
        };


        $videosTypes = $this->managers->videosManager->selectTypes();


        return $this->twig->render('Admin/categories.html.twig', [
            'media_type' => 'videos',
            'media_fr' => 'musique',
            'categories' => $videosCategories,
            'types' => $videosTypes
        ]);
    }
}
