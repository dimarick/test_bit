<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RepositoryRegistry;
use Symfony\Component\HttpFoundation\Request;

class FormAuthenticator
{
    /**
     * @var RepositoryRegistry
     */
    private $registry;

    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @param RepositoryRegistry $registry
     * @param PasswordEncoder $encoder
     */
    public function __construct(RepositoryRegistry $registry, PasswordEncoder $encoder)
    {
        $this->registry = $registry;
        $this->encoder = $encoder;
    }

    /**
     * @param Request $request
     * @return User|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function authenticateForm(Request $request): ?User
    {
        $email = $request->get('email');
        $password = $request->get('password');

        /** @var UserRepository $userRepository */
        $userRepository = $this->registry->getRepository(UserRepository::class);

        $user = $userRepository->loadUserByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!$this->encoder->isPasswordMatch($password, $user->getPassword())) {
            return null;
        }

        $session = $request->getSession();

        if ($session === null) {
            return null;
        }

        $session->set('authenticatedUser', $user->getEmail());

        return $this->user = $user;
    }

    /**
     * @param Request $request
     * @return User|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function authenticateSession(Request $request): ?User
    {
        $session = $request->getSession();

        if ($session === null) {
            return null;
        }

        $email = $session->get('authenticatedUser');

        if ($email === null) {
            return null;
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->registry->getRepository(UserRepository::class);

        $user = $userRepository->loadUserByEmail($email);

        if ($user === null) {
            return null;
        }

        return $this->user = $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function isAuthenticated(Request $request): bool
    {
        $session = $request->getSession();

        if ($session === null) {
            return false;
        }

        return $session->get('authenticatedUser') !== null;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}