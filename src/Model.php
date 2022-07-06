<?php
namespace Injix\Orm;

class Model
{  
    public int $id;
    public ?int $parentId;
    public array $parentIds;
    public array $childs;
}
