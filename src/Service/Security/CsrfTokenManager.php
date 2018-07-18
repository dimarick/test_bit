<?php

declare(strict_types=1);

namespace App\Service\Security;

use Symfony\Component\HttpFoundation\Request;

class CsrfTokenManager
{
    /**
     * @param string $name
     * @param Request $request
     * @return string
     */
    public function generate(string $name, Request $request): string
    {
        $strong = false;
        $token = \sodium_bin2hex(\openssl_random_pseudo_bytes(32, $strong));
        $request->getSession()->set('csrf_token.' . $name, $token);

        return $token;
    }

    /**
     * @param string $name
     * @param Request $request
     * @return bool
     */
    public function isValid(string $name, Request $request): bool
    {
        $token = $request->get('_token');

        if ($token === null) {
            return false;
        }

        $storedToken = $request->getSession()->get('csrf_token.' . $name);

        try {
            if (sodium_compare($token, $storedToken) === 0) {
                return true;
            }
        } catch (\SodiumException $e) {
            return false;
        }

        return false;
    }
}