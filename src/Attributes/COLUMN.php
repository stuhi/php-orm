<?php
namespace Orm\Attributes;

use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class COLUMN
{
    public function __construct(string $name)
    {
    }
}
