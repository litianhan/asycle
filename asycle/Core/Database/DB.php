<?php

namespace Asycle\Core\Database;


/**
 * Date: 2017/7/9
 * Time: 16:20
 */
class DB
{
    protected $pdo = null;
    protected $database = '';
    protected $table = null;
    protected $tablePrefix = '';
    protected $saveQuery = false;
    protected $queryRecords = [];
    final public function __construct($connection)
    {
        if ( ! class_exists("PDO")) {
            die('请先开启PDO扩展');
        }

        $config =  APP_CLIENT_DATABASE ;
        if( ! isset($config[$connection])){
            throw new \InvalidArgumentException('数据库连接配置不存在：'.$connection);
        }
        $dbConfig= &$config[$connection];
        $charset = $dbConfig['charset'] ?? 'utf8';
        $dsn = $dbConfig['dbms'] . ':host=' . rawurldecode($dbConfig['hostname']) . ';dbname=' . $dbConfig['database'] . ';charset=' . $charset;
        $options = [\PDO::ATTR_PERSISTENT => $dbConfig['pconnect']];
        $this->database = $dbConfig['database'] ?? '';
        $this->tablePrefix = $dbConfig['table_prefix'] ?? '';
        $this->pdo = new \PDO($dsn, $dbConfig['username'] ?? '', $dbConfig['password'] ?? '', $options);
        if(($dbConfig['debug'] ?? false)){
            $this->saveQuery = true;
        }
    }
    public function getDatabase(){
        return $this->database;
    }
    public function getTablePrefix(){
        return $this->tablePrefix;
    }
    public function table($table){
        if( ! ($this->table instanceof Model)){
            $this->table = new Model();
        }
        $this->table->setTable($table);
        $this->table->setConnectDB($this);
        return $this->table;
    }
   /* public function errorException($enable = true){
        if($enable){
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }else{
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        }

    }*/
    public function query($statement)
    {
        if (! $this->saveQuery) {
            return $this->pdo->query($statement);
        }
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $res = $this->pdo->query($statement);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $this->queryRecords [] = [$statement, $startTime, $endTime,$startMemory,$endMemory];
        return $res;
    }

    public function exec($statement)
    {

        if (!$this->saveQuery) {
            return $this->pdo->exec($statement);
        }
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $res = $this->pdo->exec($statement);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $this->queryRecords [] = [$statement, $startTime, $endTime,$startMemory,$endMemory];
        return $res;
    }

    public function saveQuery(bool $save = true)
    {
        $this->saveQuery = $save;
    }

    public function getQueryRecords()
    {
        return $this->queryRecords;
    }

    public function lastQueryString()
    {
        if (empty($this->queryRecords)) {
            return '';
        }
        $record = end($this->queryRecords);
        return $record[0] ?? '';
    }

    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    public function errorCode()
    {
        return $this->pdo->errorCode();
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function getAttribute($attribute)
    {
        return $this->pdo->getAttribute($attribute);
    }

    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function prepare($statement, array $driver_options = [])
    {
        return $this->pdo->prepare($statement, $driver_options);
    }

    public function quote($string, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameter_type);
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }
    public function close(){
            $this->pdo = null;
    }
}