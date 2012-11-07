<?php
namespace Nitronet\commands;

use Symfony\Component\Console\Command\Command;
use Fwk\Core\ServicesAware;
use Fwk\Xml\Map;
use Fwk\Xml\XmlFile;

abstract class BaseCommand extends Command implements ServicesAware
{
    /**
     * @var \Fwk\Core\Object
     */
    protected $services;

    /**
     * @return \Fwk\Core\Object
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param \Fwk\Core\Object $container
     *
     * @return void
     */
    public function setServices($container)
    {
        $this->services = $container;
    }
}