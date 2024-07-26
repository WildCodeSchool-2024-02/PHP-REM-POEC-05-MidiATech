<?php

namespace App\Controller;

use App\Model\BooksManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;
use App\Model\CollectionsManager;
use App\Model\ReservationsManager;
use App\Model\StocksManager;

class AdminController extends AbstractController
{
    public function index(): string
    {
        return $this->twig->render('Admin/index.html.twig');
    }

    public function collections(): string
    {
        $booksManager = new BooksManager();
        $books = $booksManager->selectAll();

        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectAll();

        $videosManager = new VideosManager();
        $videos = $videosManager->selectAll();

        return $this->twig->render('Admin/collections.html.twig', [
            'books' => $books,
            'music' => $music,
            'videos' => $videos
        ]);
    }

    public function reservations(): string
    {
        $reservationsManager = new ReservationsManager();
        $reservations = $reservationsManager->selectAll();

        return $this->twig->render('Admin/reservations.html.twig', [
            'reservations' => $reservations
        ]);
    }

    public function accept($id)
    {
        $reservationsManager = new ReservationsManager();
        $reservationsManager->acceptReservation($id);
        header('Location: /admin/reservations');
    }

    public function refuse($id)
    {
        $reservationsManager = new ReservationsManager();
        $reservationsManager->refuseReservation($id);
        header('Location: /admin/reservations');
    }

    public function schedule($id, $date)
    {
        $reservationsManager = new ReservationsManager();
        $reservationsManager->scheduleReservation($id, $date);
        header('Location: /admin/reservations');
    }

    public function stocks(): string
    {
        $booksManager = new BooksManager();
        $books = $booksManager->selectAll();

        $musicsManager = new MusicsManager();
        $music = $musicsManager->selectAll();

        $videosManager = new VideosManager();
        $videos = $videosManager->selectAll();

        return $this->twig->render('Admin/stocks.html.twig', [
            'books' => $books,
            'musics' => $music,
            'videos' => $videos
        ]);
    }
}
