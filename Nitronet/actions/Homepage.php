<?php
namespace Nitronet\actions;


use Fwk\Core\Action\Result;
use Nitronet\runtime\AppController;

class Homepage extends AppController
{
    public function show()
    {
        return Result::SUCCESS;
    }
}