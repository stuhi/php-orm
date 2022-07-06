<?php
namespace Injix\Orm;

class Bind
{
    public $name;
    public $value;
    public $type;

    public function __construct($name, $value, $type)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }
}