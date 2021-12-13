<?php

namespace Kerwin\Core;

use Kerwin\Core\Request;
use Kerwin\Core\Support\Facades\Database;

abstract class Model
{    
    /**
     * 連接的資料庫
     *
     * @var mixed
     */
    protected $connection;

    protected $table;

    protected $primaryKey = 'id';

    public function __construct() {
        $this->setTable();
        $this->setConnection(Request::createFromGlobals());
    }
    
    /**
     * 全部列數資料
     *
     * @param  mixed $columns
     * @return void
     */
    public function all(...$columns)
    {
        $columns = empty($columns) ? '*' : implode(', ', $columns);
        return Database::database($this->connection)->table($this->table)->select($columns)->get();
    }
    
    /**
     * 特定主鍵資料
     *
     * @param  string $id
     * @return void
     */
    public function find(string $id)
    {
        return Database::database($this->connection)->table($this->table)->find($id);
    }
    
    /**
     * 新增資料
     *
     * @param  array $attributes
     * @param  bool $csrf
     * @return void
     */
    public function insert(array $attributes, $csrf = false)
    {
        return Database::database($this->connection)->table($this->table)->insert($attributes, $csrf);
    }
    
    /**
     * 更新資料
     *
     * @param  string $key
     * @param  array $attributes
     * @param  bool $csrf
     * @return void
     */
    public function update(string $key, array $attributes, $csrf = false)
    {
        return Database::database($this->connection)->table($this->table)
            ->where("{$this->primaryKey} = '{$key}'")
            ->update($attributes, $csrf);
    }
    
    /**
     * 刪除資料
     *
     * @param  string $key
     * @return void
     */
    public function delete(string $key)
    {
        return Database::database($this->connection)->table($this->table)
            ->where("{$this->primaryKey} = '{$key}'")
            ->delete();
    }
    
    /**
     * 取得資料表
     *
     * @return void
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * 設定資料表
     *
     * @param  mixed $table
     * @return void
     */
    public function setTable($table = NULL)
    {
        if (is_null($table)) {
            $table = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($table).'s';
        }
        else {
            $this->table = $table;
        }
        
        return $this;
    }
    
    /**
     * 取得連接資料庫
     *
     * @return void
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    /**
     * 設定連接資料庫
     *
     * @param  \Kerwin\Core\Request $request
     * @param  mixed $connection
     * @return void
     */
    public function setConnection(Request $request, $connection = NULL)
    {
        if (is_null($connection)) {
            $this->connection = $request->server->get('DB_DATABASE');
        }
        else {
            $this->connection = $connection;
        }

        return $this;
    }
}
