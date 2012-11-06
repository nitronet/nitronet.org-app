<?php
namespace Nitronet\runtime;

use Fwk\Core\ServicesAware,
    Fwk\Core\ContextAware,
    Fwk\Core\Preparable,
    Fwk\Core\Object,
    Fwk\Core\Context;

abstract class AppController implements ServicesAware, Preparable, ContextAware
{
    /**
     * @var Object
     */
    protected $services;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @return void
     */
    public function prepare()
    {
        $this->basePath = $this->getContext()->getRequest()->getBasePath();
    }

    /**
     *
     * @return array
     */
    public function getPackages()
    {
        return $this->services->get('packagesDao')->getAllPackages();
    }

    /**
     * @return Object
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     *
     * @param mixed $container DI Container
     *
     * @return void
     */
    public function setServices($container)
    {
        $this->services = $container;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     *
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }
}