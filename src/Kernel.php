<?php

declare(strict_types=1);

namespace App;

use App\Controller\ExceptionController;
use App\Service\RepositoryRegistry;
use App\Service\Security\Authorization;
use App\Service\Security\CsrfTokenManager;
use App\Service\Security\FormAuthenticator;
use App\Service\Security\PasswordEncoder;
use App\Service\Templating;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router;
use Symfony\Component\Yaml\Yaml;

class Kernel
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Doctrine\DBAL\Portability\Connection
     */
    private $connection;

    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * @var FormAuthenticator
     */
    private $authenticator;

    /**
     * @var CsrfTokenManager
     */
    private $csrfTokenManager;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Templating
     */
    private $templating;

    public function boot(): void
    {
        $configLocator = new FileLocator([__DIR__ . '/../config']);

        $this->config = Yaml::parseFile($configLocator->locate('parameters.yaml'));

        $this->router = new Router(new YamlFileLoader($configLocator), 'routing.yaml');

        $this->connection = new Connection($this->config['connection'], new Driver(), new Configuration());
        $this->repositoryRegistry = new RepositoryRegistry($this->connection);
        $this->encoder = new PasswordEncoder();
        $this->authenticator = new FormAuthenticator($this->repositoryRegistry, $this->encoder);
        $this->authorization = new Authorization($this->authenticator);
        $this->csrfTokenManager = new CsrfTokenManager();
        $this->templating = new Templating($this, new FileLocator([__DIR__ . '/../templates']));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handleRequest(Request $request): Response
    {
        $pdoSessionHandler = new PdoSessionHandler($this->connection->getWrappedConnection(), [
            'lock_mode' => LockMode::NONE
        ]);
        $sessionStorage = new NativeSessionStorage([], $pdoSessionHandler);
        $request->setSession(new Session($sessionStorage));

        $route = $this->router->matchRequest($request);

        $this->authenticator->authenticateSession($request);

        [$class, $method] = explode('::', $route['_controller'], 2);

        $controller = new $class($this);

        return $controller->$method($request);
    }

    /**
     * @param Request $request
     * @param \Throwable $e
     * @return Response
     */
    public function handleException(Request $request, \Throwable $e): Response
    {
        $controller = new ExceptionController($this);

        return $controller->exceptionAction($request, $e);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function shutdown(Request $request, Response $response): void
    {
        $session = $request->getSession();

        if ($session === null) {
            return;
        }

        $session->save();
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return \Doctrine\DBAL\Portability\Connection
     */
    public function getConnection(): \Doctrine\DBAL\Portability\Connection
    {
        return $this->connection;
    }

    /**
     * @return RepositoryRegistry
     */
    public function getRepositoryRegistry(): RepositoryRegistry
    {
        return $this->repositoryRegistry;
    }

    /**
     * @return PasswordEncoder
     */
    public function getEncoder(): PasswordEncoder
    {
        return $this->encoder;
    }

    /**
     * @return FormAuthenticator
     */
    public function getAuthenticator(): FormAuthenticator
    {
        return $this->authenticator;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization(): Authorization
    {
        return $this->authorization;
    }

    /**
     * @return CsrfTokenManager
     */
    public function getCsrfTokenManager(): CsrfTokenManager
    {
        return $this->csrfTokenManager;
    }

    /**
     * @return Templating
     */
    public function getTemplating(): Templating
    {
        return $this->templating;
    }
}