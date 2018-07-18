<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Service\Exception\AccessDeniedHttpException;

class Authorization
{
    /**
     * @var FormAuthenticator
     */
    private $formAuthenticator;

    /**
     * @param FormAuthenticator $formAuthenticator
     */
    public function __construct(FormAuthenticator $formAuthenticator)
    {
        $this->formAuthenticator = $formAuthenticator;
    }

    /**
     *
     */
    public function denyUnlessAuthenticated(): void
    {
        $user = $this->formAuthenticator->getUser();

        if ($user === null) {
            throw new AccessDeniedHttpException();
        }
    }
}