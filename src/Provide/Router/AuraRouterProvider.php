<?php
/**
 * This file is part of the BEAR.AuraRouterModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace BEAR\Package\Provide\Router;

use Aura\Router\Router;
use BEAR\AppMeta\AbstractAppMeta;
use BEAR\Package\Provide\Router\Exception\InvalidRouterFilePathException;
use BEAR\Sunday\Annotation\DefaultSchemeHost;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

class AuraRouterProvider implements ProviderInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $schemeHost;

    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    /**
     * @var string
     */
    private $routerFile;

    /**
     * @DefaultSchemeHost("schemeHost")
     */
    public function __construct(AbstractAppMeta $appMeta, $schemeHost)
    {
        $this->schemeHost = $schemeHost;
        $this->appMeta = $appMeta;
    }

    /**
     * @param AuraRoute $router
     *
     * @Inject
     * @Named("router=aura_router,routerFile=aura_router_file")
     */
    public function setRouter($router, $routerFile = null)
    {
        $this->router = $router;
        $this->routerFile = ($routerFile === null) ? $this->appMeta->appDir . '/var/conf/aura.route.php' : $routerFile;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $router = $this->router; // global
        if (! file_exists($this->routerFile)) {
            throw new InvalidRouterFilePathException($this->routerFile);
        }
        include $this->routerFile;

        return new AuraRouter($this->router, $this->schemeHost, new HttpMethodParams);
    }
}
