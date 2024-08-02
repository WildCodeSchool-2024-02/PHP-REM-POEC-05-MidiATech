<?php

namespace App\Controller;

use App\Model\VideosManager;

class AdminvideosController extends AdminController
{
    public function categories(): string
    {
        $video = $this->managers->videosManager->selectCategories();


        return $this->twig->render('Admin/categories.html.twig', [
            'media_type' => 'videos',
            'media_fr' => 'musique',
            'categories_books' => $video
        ]);
    }
}
