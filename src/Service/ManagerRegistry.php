<?php

namespace App\Service;

use App\Model\AdminManager;
use App\Model\SessionManager;
use App\Model\UserManager;
use App\Model\RoleManager;
use App\Model\TypesManager;
use App\Model\CategoriesManager;
use App\Model\BooksManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;
use App\Model\BorrowingManager;

class ManagerRegistry
{
    public SessionManager $sessionManager;
    public UserManager $userManager;
    public RoleManager $roleManager;
    public TypesManager $typesManager;
    public CategoriesManager $categoriesManager;
    public BooksManager $booksManager;
    public MusicsManager $musicsManager;
    public VideosManager $videosManager;
    public BorrowingManager $borrowingManager;

    public function __construct(
        SessionManager $sessionManager,
        UserManager $userManager,
        RoleManager $roleManager,
        TypesManager $typesManager,
        CategoriesManager $categoriesManager,
        BooksManager $booksManager,
        MusicsManager $musicsManager,
        VideosManager $videosManager,
        BorrowingManager $borrowingManager
    ) {
        $this->sessionManager = $sessionManager;
        $this->userManager = $userManager;
        $this->roleManager = $roleManager;
        $this->typesManager = $typesManager;
        $this->categoriesManager = $categoriesManager;
        $this->booksManager = $booksManager;
        $this->musicsManager = $musicsManager;
        $this->videosManager = $videosManager;
        $this->borrowingManager = $borrowingManager;
    }
}
