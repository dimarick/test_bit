<?php

declare(strict_types=1);

namespace App\Service\Exception;

use Symfony\Component\HttpFoundation\Response;

class BadRequestHttpException extends HttpException
{
    /**
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = 'Bad request', \Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $previous);
    }
}