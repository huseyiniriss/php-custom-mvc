<?php

namespace QueryBuilder;

use PDO;
use PDOException;

class Model
{
    protected $table;
    protected $alias;
    private static $joinTable;
    private static $joinType;
    private static $joinOn;
    private static $bind = [];
    private static $where = [];
    private static $select = [];
    private static $orderBy = [];
    private static $groupBy = [];
    private static $joins = [];
    private static $limit = 0;
    private static $offset = 0;
    private static $distinct = false;
    private static $conn;

    const WHERE = 1;
    const WHERE_OR = 2;
    const WHERE_NOT = 3;
    const WHERE_NULL = 4;
    const WHERE_NOT_NULL = 5;
    const WHERE_LIKE = 6;
    const WHERE_BETWEEN = 7;
    const WHERE_IN = 8;
    const WHERE_NOT_IN = 9;
    const INNER_JOIN = 10;
    const LEFT_JOIN = 11;
    const RIGHT_JOIN = 12;

    public static function select($args = [])
    {
        foreach ($args as $arg) {
            self::$select[] = $arg;
        };
        return new static;
    }

    public static function getWhere(){
        $where = empty(self::$where) ? '' : 'WHERE ';
        foreach (self::$where as $key=>$val){
            switch ($val['type']){
                case self::WHERE:
                    $condition = $key > 0 ? ' AND ' : '';
                    $where .= $condition.$val['column'].' '.$val['condition'].' '.$val['arg']; break;
                case self::WHERE_OR:
                    $condition = $key > 0 ? ' OR ' : '';
                    $where .= $condition.$val['column'].' '.$val['condition'].' '.$val['arg']; break;
                case self::WHERE_LIKE:
                    $condition = $key > 0 ? ' AND ' : '';
                    $where .= $condition.$val['column'].' LIKE '.$val['arg']; break;
            }
        }
        return $where;
    }

    public static function where($column, $arg, $condition = '=')
    {
        self::$where[] = [
            'type' => self::WHERE,
            'column' => $column,
            'arg' => '?',
            'condition' => $condition,
        ];
        self::$bind[] = $arg;
        return new static;
    }

    public static function orWhere($column, $arg, $condition = '=')
    {
        self::$where[] = [
            'type' => self::WHERE_OR,
            'column' => $column,
            'arg' => '?',
            'condition' => $condition,
        ];
        self::$bind[] = $arg;
        return new static;
    }

    public static function whereNot($column, $arg)
    {
        self::$where[] = [
            'type' => self::WHERE_NOT,
            'column' => $column,
            'arg' => $arg,
        ];
        return new static;
    }

    public static function whereNull($column, $arg){
        self::$where[] = [
            'type' => self::WHERE_NULL,
            'column' => $column,
            'arg' => $arg,
        ];
        return new static;
    }

    public static function whereNotNull($column, $arg){
        self::$where[] = [
            'type' => self::WHERE_NOT_NULL,
            'column' => $column,
            'arg' => $arg,
        ];
        return new static;
    }

    public static function whereLike($column, $arg){
        self::$where[] = [
            'type' => self::WHERE_LIKE,
            'column' => $column,
            'arg' => $arg,
        ];
        return new static;
    }

    public static function whereBetween($column, $arg){
        self::$where[] = [
            'type' => self::WHERE_BETWEEN,
            'column' => $column,
            'arg' => $arg,
        ];
    }

    public static function whereIn($column, $args = [])
    {
        self::$where[] = [
            'type' => self::WHERE_IN,
            'column' => $column,
            'arg' => $args,
        ];
        return new static;
    }

    public static function whereNotIn($column, $args = [])
    {
        self::$where[] = [
            'type' => self::WHERE_NOT_IN,
            'column' => $column,
            'arg' => $args,
        ];
        return new static;
    }

    public static function when($condition, $query){
        if ($condition){
            $query(new static);
            return new static;
        }
        return false;
    }

    public static function distinct()
    {
        self::$distinct = true;
        return new static;
    }

    public static function orderBy($column, $sortType = 'ASC')
    {
        self::$orderBy[] = [$column, $sortType];
        return new static;
    }

    public static function on($on){
        self::$joinOn[] = $on;
        return new static;
    }

    public static function getJoins(){
        $joins = '';
        $joinType = '';
        foreach (self::$joins as $join){
            switch ($join['type']){
                case self::INNER_JOIN: $joinType = 'INNER'; break;
                case self::LEFT_JOIN: $joinType = 'LEFT'; break;
                case self::RIGHT_JOIN: $joinType = 'RIGHT'; break;
            }
            $joins .= $joinType.' JOIN '.$join['table'].' ON '. implode($join['on'], ' AND '). PHP_EOL;
        }
        return $joins;
    }

    public static function join($table, $join){
        if ($table){
            self::$joinTable = $table;
            self::$joinType = self::INNER_JOIN;
            $join(new static);
            self::$joins[] = [
                'type' => self::$joinType,
                'table' => self::$joinTable,
                'on' => self::$joinOn,
            ];
            self::$joinOn = [];
            return new static;
        }
        return false;
    }

    public static function leftJoin($table, $join){
        if ($table){
            self::$joinTable = $table;
            self::$joinType = self::LEFT_JOIN;
            $join(new static);
            self::$joins[] = [
                'type' => self::$joinType,
                'table' => self::$joinTable,
                'on' => self::$joinOn,
            ];
            self::$joinOn = [];
            return new static;
        }
        return false;
    }

    public static function rightJoin($table, $join){
        if ($table){
            self::$joinTable = $table;
            self::$joinType = self::RIGHT_JOIN;
            $join(new static);
            self::$joins[] = [
                'type' => self::$joinType,
                'table' => self::$joinTable,
                'on' => self::$joinOn,
            ];
            self::$joinOn = [];
            return new static;
        }
        return false;
    }

    public static function groupBy($column = []){
        self::$groupBy = $column;
        return new static;
    }

    public static function limit($limit, $offset = 0){
        self::$limit = $limit;
        self::$offset= $offset;
        return new static;
    }

    public static function getLastQuery()
    {
        $instance = new static;
        $table = $instance->table;
        $alias = $instance->alias;
        $distinct = self::$distinct ? 'DISTINCT' : '';
        $limit = self::$limit === 0 ? '' : 'LIMIT '.self::$limit;
        $offset = self::$offset === 0 ? '' : ', '.self::$offset;
        $groupBy = count(self::$groupBy) === 0 ? '': 'GROUP BY '.implode(self::$groupBy, ',');
        $joins = self::getJoins();
        $select = empty(self::$select) ? '*' : implode(self::$select, ',');
        $where = self::getWhere();
        return "SELECT $distinct $select FROM $table $alias $where $joins $groupBy $limit$offset";
    }

    public static function insert($data){
        $instance = new static;
        $table = $instance->table;
        $keys = implode(',', array_keys($data));
        $values = ':'.implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";
        $res = self::$conn->prepare($sql)->execute($data);
        return $res ? self::$conn->lastInsertId() : false;
    }

    public static function getLastInsertId(){
        return self::$conn->lastInsertId();
    }

    public static function get(){
        $sql = self::getLastQuery();
        $query = self::$conn->prepare($sql);
        $query->execute(self::$bind);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        die(json_encode($result));
    }

    public static function connectMYSQL($config, $username, $password){
        try{
            $configStr = '';
            foreach ($config as $key=>$c){
                $configStr .= "$key=$c;";
            }
            $conn = new PDO("mysql:$configStr", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn = $conn;
        }catch (PDOException $e){
            die($e->getMessage());
        }
    }
}
