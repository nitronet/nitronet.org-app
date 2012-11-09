<?php
namespace Nitronet\runtime;

use Fwk\Core\ServicesAware,
    Fwk\Core\ContextAware,
    Fwk\Core\Object,
    Fwk\Core\Context;

abstract class AppController implements ServicesAware, ContextAware
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
}