<?php

declare(strict_types=1);

namespace App\Service;

use App\Kernel;
use Symfony\Component\Config\FileLocatorInterface;

class Templating
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @param Kernel $kernel
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(Kernel $kernel, FileLocatorInterface $fileLocator)
    {
        $this->kernel = $kernel;
        $this->fileLocator = $fileLocator;
    }

    /**
     * @param string $templateName
     * @param array $vars
     * @return string
     */
    public function renderTemplate(string $templateName, array $vars = []): string
    {
        extract($vars, EXTR_OVERWRITE);
        $kernel = $this->kernel;
        ob_start();
        try {
            include $this->fileLocator->locate($templateName);

            return ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    public function escape(string $string): string
    {
        return htmlspecialchars($string);
    }
}