<?php

declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use App\Routes;
use App\Service\Exception\BadCsrfHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        if ($this->kernel->getAuthenticator()->isAuthenticated($request)) {
            return new RedirectResponse($this->kernel->getRouter()->generate(Routes::PROFILE));
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            if (!$this->kernel->getCsrfTokenManager()->isValid('login', $request)) {
                throw new BadCsrfHttpException();
            }

            $user = $this->kernel->getAuthenticator()->authenticateForm($request);

            if ($user === null) {
                return new Response('Failed to login. Unknown username or password. Try again');
            }

            return new RedirectResponse($this->kernel->getRouter()->generate(Routes::PROFILE));
        }

        return new Response($this->kernel->getTemplating()->renderTemplate('Security/login.php', [
            'request' => $request
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function logoutAction(Request $request): Response
    {
        if (!$this->kernel->getCsrfTokenManager()->isValid('logout', $request)) {
            throw new BadCsrfHttpException();
        }

        if ($this->kernel->getAuthenticator()->isAuthenticated($request)) {
            $request->getSession()->invalidate();
        }

        return new RedirectResponse($this->kernel->getRouter()->generate(Routes::HOME));
    }
}