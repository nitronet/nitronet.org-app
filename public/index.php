<?php
require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Application,
    Fwk\Core\Descriptor;

$desc = new Descriptor(__DIR__ .'/../Nitronet/fwk.xml');
$app = new Application($desc);
$app->run(\Symfony\Component\HttpFoundation\Request::createFromGlobals());
