<?php
namespace Injix\Orm;

use \PDO;

class Db extends PDO
{
    public function __construct(string $server, string $dbname, string $user, string $password)
    {
        parent::__construct('mysql:host=' . $server . ';dbname=' . $dbname, $user, $password);
    }

    public function execute(string $query, array $binds = array())
    {
        $stmt = $this->prepare($query);
        $countBinds = count($binds);
        for ($i = 0; $i < $countBinds; $i++)
        {
            $stmt->bindValue($binds[$i]->name, $binds[$i]->value, $binds[$i]->type);
        }
        $stmt->execute();
    }

    public function fetchScalar(Type $type, string $query, array $binds = array())
    {
        $stmt = $this->prepare($query);
        $countBinds = count($binds);
        for ($i = 0; $i < $countBinds; $i++)
        {
            $stmt->bindValue($binds[$i]->name, $binds[$i]->value, $binds[$i]->type);
        }
        $stmt->execute();
        $result = $stmt->fetchColumn();

        if ($type == Type::Int) return intval($result);
        else if ($type == Type::Bool) return boolval($result);
        else return $result;
    }
    
    public function fetchArray(string $key, string $value, string $query, array $binds = array())
    {
        $array = array();
        $stmt = $this->prepare($query);
        $countBinds = count($binds);
        for ($i = 0; $i < $countBinds; $i++)
        {
            $stmt->bindValue($binds[$i]->name, $binds[$i]->value, $binds[$i]->type);
        }
        $stmt->execute();
        if (empty($key))
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $array[] = $row[$value];
            }
        }
        else
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $array[$row[$key]] = $row[$value];
            }
        }
        return $array;
    }

    public function fetchModel(string $modelName, string $query, array $binds = array())
    {
        $model = null;
        $stmt = $this->prepare($query);
        $countBinds = count($binds);
        for ($i = 0; $i < $countBinds; $i++)
        {
            $stmt->bindValue($binds[$i]->name, $binds[$i]->value, $binds[$i]->type);
        }
        $stmt->execute();
        $mapping = new Mapping($modelName);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $model = $mapping->mapping($row);
        }
        return $model;
    }

    public function fetchArrayModel(string $modelName, string $query, array $binds = array())
    {
        $arrayModel = array();
        $stmt = $this->prepare($query);
        $countBinds = count($binds);
        for ($i = 0; $i < $countBinds; $i++)
        {
            $stmt->bindValue($binds[$i]->name, $binds[$i]->value, $binds[$i]->type);
        }
        $stmt->execute();
        $mapping = new Mapping($modelName);
        if ($mapping->hasId && $mapping->hasParentId)
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $model = $mapping->mapping($row);
                if ($model->parentId == null) $arrayModel[$model->id] = $model;
                else $this->_setChild($arrayModel, $model, 0);
            }
        }
        else if ($mapping->hasId)
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $model = $mapping->mapping($row);
                $arrayModel[$model->id] = $model;
            }
        }
        else
        {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $arrayModel[] = $mapping->mapping($row);
            }
        }
        return $arrayModel;
    }

    private function _setChild($arrayModel, $model, $i)
    {
        if (count($model->parentIds) > $i)
        {
            if ($model->parentIds[$i] == $model->parentId) $arrayModel[$model->parentIds[$i]]->childs[$model->id] = $model;
            else $this->_setChild($arrayModel[$model->parentIds[$i]]->childs, $model, $i++);
        }
    }
}