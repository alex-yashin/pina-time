<?php

namespace PinaTime;

use Pina\App;

use Pina\ModuleInterface;
use PinaTime\Types\DateTimeType;
use Pina\Types\TimestampType;

class Module implements ModuleInterface
{

    public function __construct()
    {
        App::types()->set(TimestampType::class, DateTimeType::class);
    }

    public function getPath()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'Time';
    }

    public function http()
    {
    }

    public function cli()
    {
        return [];
    }

}