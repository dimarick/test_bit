<?php

declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use App\Service\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ExceptionController
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
     * @param \Throwable $e
     * @return Response
     */
    public function exceptionAction(Request $request, \Throwable $e): Response
    {
        if ($e instanceof HttpException) {
            return new Response($e->getMessage(), $e->getCode());
        }

        if ($e instanceof ResourceNotFoundException || $e instanceof MethodNotAllowedException) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        error_log(sprintf(
            "Unexpected error occured: %s(%s, %s) \n%s\n==================================\n",
            get_class($e),
            $e->getMessage(),
            $e->getCode(),
            $e->getTraceAsString()
        ), 0);

        return new Response('Internal server error', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}