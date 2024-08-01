<?php

namespace App\Controller;

use Twig\Environment;
use App\Service\ManagerRegistry;

/**
 * Initialized some Controller common features (Twig...)
 */
abstract class AbstractController
{
    public const USER = 'user';
    public const ADMIN = 'admin';

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

    public function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }
}
