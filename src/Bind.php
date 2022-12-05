<?php
namespace Stuhi\Orm;

use PDO;

class Bind
{
    public $name;
    public $value;
    public $type;

    public function __construct(string $name, mixed $value, Type $type)
    {
        $this->name = $name;
        $this->value = self::_setValue($value, $type);
        $this->type = self::_setType($type);
    }

    private static function _setType($type)
    {
        switch($type)
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

    private static function _setValue($value, $type)
    {
        switch($type)
        {
            case Type::Int: return $value;
            case Type::Bool: return $value;
            case Type::Date: return $value->format('Y-m-d');
            case Type::DateTime: return $value->format('Y-m-d H:i:s');
            case Type::String: return $value;
            case Type::Array: return count($value) > 0 ? ('|' . implode('|', $value) . '|') : '';
        }
        return PDO::PARAM_STR;
    }
}
