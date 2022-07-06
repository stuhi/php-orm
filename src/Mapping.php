<?php
namespace Injix\Orm;

use \ReflectionClass;
use \ReflectionProperty;
use \DateTime;

class Mapping
{
    private array $types = array();
    private array $nulls = array();
    private array $columns = array();
    private array $columnKeys = array();
    private array $rowKeys = array();

    public bool $hasId = false;
    public bool $hasParentId = false;
    
    private $class;

    public function __construct($className)
    {
        $class = (new ReflectionClass($className));
        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);
        $classAttrs = $class->getAttributes('Injix\Orm\Attributes\ID');
        if (count($classAttrs) > 0)
        {
            $countArguments = count($classAttrs[0]->getArguments());
            for ($i = 0; $i < $countArguments; $i++)
            {
                $column = $classAttrs[0]->getArguments()[$i];
                $this->columnKeys[] = $column;
                if ($i == 0)
                {
                    $this->types[$column] = 'int';
                    $this->nulls[$column] = false;
                    $this->columns[$column] = 'id';
                    $this->hasId = true;
                }
                else if ($i == 1)
                {
                    $this->types[$column] = 'int';
                    $this->nulls[$column] = true;
                    $this->columns[$column] = 'parentId';
                    $this->hasParentId = true;
                }
                else if ($i == 2)
                {
                    $this->types[$column] = 'array';
                    $this->nulls[$column] = false;
                    $this->columns[$column] = 'parentIds';
                    $this->hasParentId = true;
                }                
            }
        }

        foreach ($properties as $property) 
        {
            $name = $property->getName();
            $propertyAttrs = $property->getAttributes('Injix\Orm\Attributes\COLUMN');
            if (count($propertyAttrs) > 0)
            {
                $column = $propertyAttrs[0]->getArguments()[0];            
                $this->types[$column] = $property->getType()->getName();            
                $this->nulls[$column] = $property->getType()->allowsNull();
                $this->columns[$column] = $name;
                $this->columnKeys[] = $column;
            }
        }
        $this->class = $className;
    }

    public function mapping($row)
    {
        if (count($this->rowKeys) == 0) 
        {
            $this->rowKeys = array_keys($row);
        }

        $className = $this->class;
        $class = new $className();
        foreach ($this->rowKeys as $key) 
        {
            if (in_array($key, $this->columnKeys))
            {
                $prop = $this->columns[$key];
                $type = $this->types[$key];
                $null = $this->nulls[$key];
                $value = $row[$key];
                if ($value == null && $null)
                {
                    $class->{$prop} = null;
                }
                else
                {
                    switch ($type) 
                    {
                        case 'string': $class->{$prop} = $value; break;
                        case 'int': $class->{$prop} = intval($value);break;
                        case 'bool': $class->{$prop} = boolval($value); break;
                        case 'DateTime': $class->{$prop} = new DateTime($value); break;
                        case 'array': $class->{$prop} = $this->_mapArray($value); break;
                    }

                }
            }
        }
        
        return $class;
    }

    private function _mapArray(string $value) : array
    {
        return (!empty($value)) ? array_filter(explode('|', $value), function ($item) { return !empty($item); }) : array();
    }
}