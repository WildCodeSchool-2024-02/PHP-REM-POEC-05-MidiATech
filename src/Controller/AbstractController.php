<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;
use App\Model\UserManager;

/**
 * Initialized some Controller common features (Twig...)
 */
abstract class AbstractController
{
    protected Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(APP_VIEW_PATH);
        $this->twig = new Environment(
            $loader,
            [
                'cache' => false,
                'debug' => true,
            ]
        );
        $this->twig->addExtension(new DebugExtension());
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addGlobal('app', ['user' => $this->getUser()]);
    }

    private function getUser(): false|array|null
    {
        if (isset($_SESSION['user_id'])) {
            $userManager = new UserManager();
            return $userManager->selectOneById($_SESSION['user_id']);
        }

        if (isset($_COOKIE['user_id'])) {
            $userManager = new UserManager();
            return $userManager->selectOneById($_COOKIE['user_id']);
        }
        return null;
    }
}
