<?php
namespace Orm;

use PDO;

class Bind
{
    public $name;
    private $value;
    private $type;

    public function __construct(string $name, mixed $value, Type $type)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function getType()
    {
        switch($this->type)
        {
            case Type::Int: return PDO::PARAM_INT;
            case Type::Bool: return PDO::PARAM_BOOL;
            case Type::Date:
            case Type::DateTime:
            case Type::Array:
            case Type::String: return PDO::PARAM_STR;
        }
        return PDO::PARAM_STR;
    }

    public function getValue(string $server)
    {
        switch($server)
        {
            case 'mysql': return self::_getValueMySQL();
            case 'pgsql': return self::_getValuePgSQL();
        }
        return '';
    }

    private function _getValueMySQL()
    {
        switch($this->type)
        {
            case Type::Int: return $this->value;
            case Type::Bool: return $this->value;
            case Type::Date: return $this->value->format('Y-m-d');
            case Type::DateTime: return $this->value->format('Y-m-d H:i:s');
            case Type::String: return $this->value;
            case Type::Array: return count($this->value) > 0 ? ('|' . implode('|', $this->value) . '|') : '';
        }
        return '';
    }

    private function _getValuePgSQL()
    {
        switch($this->type)
        {
            case Type::Int: return $this->value;
            case Type::Bool: return $this->value;
            case Type::Date: return $this->value->format('Y-m-d');
            case Type::DateTime: return $this->value->format('Y-m-d H:i:s');
            case Type::String: return $this->value;
            case Type::Array: return count($this->value) > 0 ? ('{' . implode(',', $this->value) . '}') : null;
        }
        return '';
    }
}
