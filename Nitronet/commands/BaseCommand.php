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
     *
     * @param string $xmlContents
     * @param \Fwk\Xml\Map $map
     *
     * @return array
     */
    public function parseXml($xmlContents, Map $map)
    {
        $tmpFile    = tempnam(sys_get_temp_dir(), 'fwkxml-');
        file_put_contents($tmpFile, $xmlContents);

        $xml        = new XmlFile($tmpFile);
        $results    = $map->execute($xml);

        unlink($tmpFile);

        return $results;
    }

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