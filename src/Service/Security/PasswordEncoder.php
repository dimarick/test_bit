<?php

declare(strict_types=1);

namespace App\Service\Security;

class PasswordEncoder
{
    /**
     * @param string $plainText
     * @return string hash
     */
    public function encodePassword(string $plainText): string
    {
        return \sodium_crypto_pwhash_str($plainText, SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE, SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }

    /**
     * @param string $plainText
     * @param string $storedPassword
     * @return bool
     */
    public function isPasswordMatch(string $plainText, string $storedPassword): bool
    {
        return \sodium_crypto_pwhash_str_verify($storedPassword, $plainText);
    }
}