<?php

declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use App\Service\Exception\BadCsrfHttpException;
use App\Service\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController
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
    public function homeAction(Request $request): Response
    {
        return new Response($this->kernel->getTemplating()->renderTemplate('User/home.php', [
            'request' => $request
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \App\Service\Exception\HttpException
     */
    public function profileAction(Request $request): Response
    {
        $this->kernel->getAuthorization()->denyUnlessAuthenticated();

        return new Response($this->kernel->getTemplating()->renderTemplate('User/profile.php', [
            'request' => $request,
            'user' => $this->kernel->getAuthenticator()->getUser()
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \App\Service\Exception\HttpException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Throwable
     */
    public function payoutAction(Request $request): Response
    {
        $this->kernel->getAuthorization()->denyUnlessAuthenticated();

        if (!$request->isMethod(Request::METHOD_POST)) {
            return new RedirectResponse($this->kernel->getRouter()->generate('app_profile'));
        }

        if (!$this->kernel->getCsrfTokenManager()->isValid('payout', $request)) {
            throw new BadCsrfHttpException();
        }

        $sum = (float)$request->get('sum', 0.0);

        if (round($sum, 2) <= 0) {
            throw new BadRequestHttpException('Please set valid positive value');
        }

        $result = $this->kernel->getPayoutManager()->processPayout(
            $this->kernel->getAuthenticator()->getUser(),
            $sum
        );

        if ($result === false) {
            throw new BadRequestHttpException('Cannot pay');
        }

        return new RedirectResponse($this->kernel->getRouter()->generate('app_profile'));
    }
}