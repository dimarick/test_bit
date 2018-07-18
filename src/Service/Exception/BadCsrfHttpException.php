<?php

declare(strict_types=1);

namespace App\Service\Exception;

class BadCsrfHttpException extends BadRequestHttpException
{
    /**
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = 'Hacking attempt', \Throwable $previous = null)
    {
        parent::__construct($message, $previous);
    }
}