<?php
namespace Stuhi\Orm\Attributes;

use \Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ID
{
    public function __construct(string $id, string $parentid = '', string $parentids = '')
    {
    }
}
