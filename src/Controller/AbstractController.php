<?php

namespace App\Controller;

use AllowDynamicProperties;
use Twig\Environment;
use App\Model\SessionManager;
use App\Model\UserManager;
use App\Model\RoleManager;
use App\Model\TypesManager;
use App\Model\CategoriesManager;
use App\Model\BooksManager;
use App\Model\MusicsManager;
use App\Model\VideosManager;
use App\Model\BorrowingManager;
use App\Service\ManagerRegistry;

/**
 * Initialized some Controller common features (Twig...)
 */
abstract class AbstractController
{
    public const string ADMIN = 'admin';
    public const string USER = 'user';

    protected Environment $twig;
    protected ManagerRegistry $managers;

    public function __construct(
        ManagerRegistry $managers,
        Environment $twig
    ) {
        $this->managers = $managers;
        $this->twig = $twig;

        $this->managers->sessionManager->start();
        $this->twig->addGlobal('app', ['user' => $this->getUser(), 'userRole' => $this->getUserRole()]);
    }

    protected function isUserLoggedIn(): bool
    {
        return $this->managers->sessionManager->isset('user_id');
    }

    protected function getUserId(): ?int
    {
        if ($this->isUserLoggedIn()) {
            return $this->managers->sessionManager->get('user_id');
        }
        return null;
    }

    protected function getUser(): ?array
    {
        if ($this->isUserLoggedIn()) {
            return $this->managers->userManager->selectOneById($this->getUserId());
        }
        return null;
    }

    protected function getUserRole(): ?string
    {

        if ($this->isUserLoggedIn()) {
            return $this->managers->userManager->getUserRole($this->getUserId());
        }
        return null;
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
        exit();
    }
}
