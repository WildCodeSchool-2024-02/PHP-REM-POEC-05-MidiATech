<?php

namespace App\Controller;

use App\Model\MusicsManager;

class AdminMusicsController extends AdminController
{
    public function categories(): string
    {
        $music = $this->managers->musicsManager->selectCategories();


        return $this->twig->render('Admin/categories.html.twig', [
            'media_type' => 'musics',
            'media_fr' => 'musique',
            'categories' => $music
        ]);
    }
}
