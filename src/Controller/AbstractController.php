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
        $this->startSessionIfNotStarted();

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
        $this->twig->addGlobal('app', ['user' => $this->getUser(), 'userRole' => $this->getUserRole()]);
    }

    protected function getUser(): ?array
    {
        if (isset($_SESSION['user_id'])) {
            return (new UserManager())->selectOneById($_SESSION['user_id']);
        }
        return null;
    }

    protected function getUserRole()
    {

        if (isset($_SESSION['user_id'])) {
            return (new UserManager())->getUserRole($_SESSION['user_id']);
        }
        return null;
    }

    protected function isUserLoggedIn(): bool
    {
        $this->startSessionIfNotStarted();
        return isset($_SESSION['user_id']);
    }

    public function logout(): void
    {
        $this->startSessionIfNotStarted();
        session_destroy();
        header('Location: /login');
        exit();
    }

    protected function startSessionIfNotStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
