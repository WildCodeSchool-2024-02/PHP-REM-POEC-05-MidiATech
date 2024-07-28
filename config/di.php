<?php

use App\Model\BooksManager;
use App\Model\BorrowingManager;
use App\Model\CategoriesManager;
use App\Model\MusicsManager;
use App\Model\RoleManager;
use App\Model\SessionManager;
use App\Model\TypesManager;
use App\Model\UserManager;
use App\Model\VideosManager;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

return [
    // Liaisons de vos classes vont ici
    SessionManager::class => DI\create(SessionManager::class),
    UserManager::class => DI\create(UserManager::class),
    RoleManager::class => DI\create(RoleManager::class),
    TypesManager::class => DI\create(TypesManager::class),
    CategoriesManager::class => DI\create(CategoriesManager::class),
    BooksManager::class => DI\create(BooksManager::class),
    MusicsManager::class => DI\create(MusicsManager::class),
    VideosManager::class => DI\create(VideosManager::class),
    BorrowingManager::class => DI\create(BorrowingManager::class),

    Environment::class => static function () {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $twig = new Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        $twig->addExtension(new DebugExtension());
        $twig->addExtension(new IntlExtension());
        return $twig;
    },
];
