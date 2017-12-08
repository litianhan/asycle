<?php

namespace Asycle\Core\Database;
use Asycle\Core\Connection;

/**
 * Date: 2017/11/29
 * Time: 18:02
 */
class Model{
    /**
     * 数据库连接名称
     * @var string
     */
    protected $connection = '';
    /**
     * 数据库名称
     * @var string
     */
    protected $database = '';
    /**
     * 数据表名称
     * @var string
     */
    protected $table = '';
    /**
     * 数据库表前缀
     * @var string
     */
    protected $tablePrefix = '';
    /**
     * 数据表别名
     * @var string
     */
    protected $tableAsName = '';
    /**
     * 被``符号包装过的表名
     * @var string
     */
    private $wrappedTable = '';
    /**
     * 数据库连接
     * @var DB|null
     */
    private $db = null;

    private $whereStatement = '';
    private $hasClause = false;
    private $updateStatement = '';
    private $groupBy = '';
    private $selectFields = '*';
    private $orderBy = '';
    private $limitStatement = '';
    private $joinStatement = '';
    private $union = [];
    private $trustInput = false;
    private $enableWrapName = false;
    final public function __construct()
    {
        $this->setConnectDB(Connection::db($this->connection));
    }
    public function setConnectDB($db){

        $this->db = $db;
        $this->tablePrefix = $this->db->getTablePrefix();
        $this->database = $this->db->getDatabase();
        $this->wrappedTable = '`' . $this->database . '`.`' . trim($this->tablePrefix . $this->table, '`') . '`';
    }
    public function enableWrapName($enable){
        $this->enableWrapName = $enable;
    }
    /**
     * 获取被``符号包装过的表名
     * @return string
     */
    protected function getWrappedTable(): string
    {
        return $this->wrappedTable;
    }

    /**
     * 返回数据库表名称
     * @return string
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * 设置数据库表名称
     * @param $table
     */
    public function setTable($table){
        $this->table = $table;
    }
    /**
     * 返回数据库名称
     * @return string
     */
    public function getDatabase(){
        return $this->database;
    }

    /**
     * 设置数据库模名称
     * @param $database
     */
    public function setDatabase($database){
        $this->database = $database;
    }
    public function asTable($asTable){
        if ( ! empty($asTable)) {
            $this->wrappedTable .= ' AS ' . $this->wrapName($asTable);
            $this->tableAsName = $this->wrapName($asTable);
        }
        return $this;
    }
    /**
     * 是否信任输入，如果是，则不对参数进行过滤，否则使用强制模式防止SQL注入
     * @param bool $trust
     */
    public function trustInput($trust = true){
        $this->trustInput = $trust;
    }
    /**
     * 清除旧查询记录
     */
    protected function clear()
    {
        $this->selectFields = '*';
        $this->whereStatement = '';
        $this->updateStatement = '';
        $this->orderBy = '';
        $this->groupBy = '';
        $this->limitStatement = '';
        $this->joinStatement = '';
    }

    /**
     * 获取符合条件的一条记录
     * @param array $fields
     * @param bool $returnObject
     * @return bool|mixed
     */
    public function first(array $fields = [],$returnObject = false){
        $res = $this->limit(0,1)->get($fields,$returnObject);
        if(is_array($res) and isset($res[0])){
            return $res[0];
        }
        return false;
    }

    /**
     * 获取符合条件的多条记录
     * @param array $fields
     * @param bool $returnObject
     * @return array
     */
    public function get(array $fields = [],$returnObject = false)
    {
        if (! empty($fields)) {
            foreach ($fields as $key => $value) {
                if( ! empty($value)){
                    $fields[ $key ] = $this->wrapName($value);
                }
            }
            $this->selectFields = implode(',', $fields);
        }
        if(empty($this->selectFields)){
            $this->selectFields = '*';
        }

        $sql = '';
        $selectSql = 'SELECT ' . $this->selectFields . ' FROM ' . $this->getWrappedTable() . $this->joinStatement . $this->getWhereStatement() . $this->groupBy .$this->orderBy . $this->limitStatement;
        if( ! empty($this->union)){
            foreach ($this->union as $value){
                $sql.=$value;
            }
            $this->union = [];
        }
        $sql.=$selectSql;
        $res = $this->db->query($sql);

        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return $res->fetchAll($returnObject ? \PDO::FETCH_OBJ : \PDO::FETCH_ASSOC);
    }

    /**
     * 删除记录
     * @return int 成功删除的记录数
     */
    public function delete():int
    {
        $sql = 'DELETE FROM ' . $this->getWrappedTable() . $this->whereStatement;
        $res = $this->db->exec($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return intval($res);
    }

    /**
     * 清空数据库表，重置自增值
     * @return bool
     */
    public function truncate(){
        $sql = 'TRUNCATE '.$this->getWrappedTable() ;
        $res = $this->db->exec($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        return true;
    }

    /**
     * 插入一条记录并返回自增id，请确保表中存在自增id字段，否则返回0
     * @param array $record
     * @return int
     */
    public function insertOneAndGetId(array $record){
        if (empty($record)) {
            throw new \InvalidArgumentException('参数不能为空!');
        }
        $sql = 'INSERT INTO ' . $this->getWrappedTable() . '(' . $this->keysStatement($record) . ') VALUES(' . $this->valuesStatement($record) . ')';
        $res = $this->db->query($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return $this->db->lastInsertId();
    }

    /**
     * 插入语一条记录
     * @param array $records
     * @return bool
     */
    public function insertOne(array $records):bool
    {
        if (empty($records)) {
            throw new \InvalidArgumentException('参数不能为空!');
        }
        $sql = 'INSERT INTO ' . $this->getWrappedTable() . '(' . $this->keysStatement($records) . ') VALUES(' . $this->valuesStatement($records) . ')';
        $res = $this->db->query($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return true;
    }

    /**
     * 批量插入记录
     * @param array $params
     * @return int
     */
    public function insertBatch(array $params):int
    {
        if (empty($params)) {
            return 0;
        }
        $sql = 'INSERT INTO ' . $this->getWrappedTable() . '(' . $this->keysStatement($params[0]) . ') VALUES';
        $values = [];
        foreach ($params as $record) {
            $values []= '(' . $this->valuesStatement($record) . ')';
        }
        $sql .= implode(',',$values);
        $res = $this->db->exec($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return intval($res);
    }
    /**
     * 插入一条记录，如果主键重复则更新
     * @param array $insertedRecord
     * @param array $updated
     * @return \PDOStatement
     */
    public function insertOnDuplicateKeyUpdate(array $insertedRecord, array $updated)
    {
        if(empty($updated)){
            throw new \InvalidArgumentException('更新参数不能为空');
        }
        $sql = 'INSERT INTO ' . $this->getWrappedTable() . '(' . $this->keysStatement($insertedRecord) . ') VALUES(' . $this->valuesStatement($insertedRecord) . ')';
        $sql .= ' ON DUPLICATE KEY UPDATE '.$this->updateStatement.' '.$this->updateStatement($updated);
        $res = $this->db->query($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return $res;
    }
    /**
     * 聚合函数
     * @param string $name
     * @param string $field
     * @param string $as
     * @param bool $distinct
     * @return array
     */
    protected function aggregate(string $name , string $field, $as = '',$distinct = false){
        if (empty($field)) {
            throw new \InvalidArgumentException('参数不能为空!');
        }else{
            $field = $this->wrapName($field);
        }
        if($distinct){
            $statement =  $name .'(DISTINCT('.$field.'))';
        }else{
            $statement =  $name . '('.$field.')';
        }
        if( ! empty($as)){
            $statement .= ' AS '.$as;
        }
        $sql = 'SELECT '.$statement.' FROM ' . $this->getWrappedTable() . $this->getWhereStatement().$this->orderBy . $this->groupBy;
        $res = $this->db->query($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return $res->fetchAll(\PDO::FETCH_NUM);
    }

    /**
     * 返回字段最小值
     * @param string $field
     * @param string $as
     * @return mixed|null
     */
    public function min(string $field,$as = ''){
        $result = $this->aggregate('MIN',$field,$as,false);
        return $result[0] ?? null;
    }

    /**
     * 返回字段最大值
     * @param string $field
     * @param string $as
     * @return mixed|null
     */
    public function max(string $field,$as = ''){
        $result = $this->aggregate('MAX',$field,$as,false);
        return $result[0] ?? null;
    }

    /**
     * 返回字段的平均值
     * @param string $field
     * @param string $as
     * @param bool $distinct
     * @return mixed|null
     */
    public function avg(string $field,$as = '',$distinct = false){
        $result = $this->aggregate('AVG',$field,$as,$distinct);
        return $result[0] ?? null;
    }

    /**
     * 返回字段的总和
     * @param string $field
     * @param string $as
     * @return mixed|null
     */
    public function sum(string $field,$as = '')
    {
        $result = $this->aggregate('SUM',$field,$as,false);
        return $result[0] ?? null;
    }

    /**
     * 返回字段的记录数
     * @param string $field
     * @param string $as
     * @param bool $distinct
     * @return null
     */
    public function count($field = '*',$as = '',$distinct = false)
    {
        $res = $this->aggregate('COUNT',$field,$as,$distinct);
        if (isset($res[0]) and isset($res[0][0])) {
            return $res[0][0];
        }
        return null;
    }
    /**
     * 更新记录
     * @param array $params
     * @return int
     */
    public function update(array $params = []): int
    {
        $sql = 'UPDATE ' . $this->getWrappedTable();

        if(empty($this->updateStatement)){
            $this->updateStatement = ' SET '.$this->updateStatement($params);
        }elseif(! empty($params)){
            $this->updateStatement .= ',' . $this->updateStatement($params);
        }
        $sql .= $this->updateStatement;
        $sql .= $this->whereStatement;
        $res = $this->db->exec($sql);
        if ($res === false) {
            $this->throwError($sql);
        }
        $this->clear();
        return intval($res);
    }

    /**
     * 分组
     * @param $field
     * @return $this
     */
    public function groupBy($field){
        $this->groupBy = ' GROUP BY ' . $this->wrapName($field);
        return $this;
    }

    /**
     * 按字段降序排序
     * @param string $field
     * @return $this
     */
    public function orderByDesc(string $field)
    {
        $this->orderBy = ' ORDER BY ' . $this->wrapName($field) . ' DESC';
        return $this;
    }

    /**
     * 按字段升序排序
     * @param string $field
     * @return $this
     */
    public function orderByAsc(string $field)
    {
        $this->orderBy = ' ORDER BY ' . $this->wrapName($field) . ' ASC';
        return $this;
    }

    /**
     * 限制返回记录
     * @param int $offset
     * @param int $limit
     * @return $this
     */
    public function limit(int $offset, int $limit)
    {
        if(empty($offset)){
            $this->limitStatement = ' LIMIT '  . $limit;
        }else{
            $this->limitStatement = ' LIMIT ' . $offset . ',' . $limit;
        }
        return $this;
    }

    /**
     * 联合查询，去重
     * @param array $fields
     * @param $tableName
     * @return $this
     */
    public function selectAndUnion(array $fields = [],$tableName){
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $fields[ $key ] = $this->wrapName($value);
            }
            $this->selectFields = implode(',', $fields);
        } else {
            $this->selectFields = '*';
        }

        $sql = 'SELECT ' . $this->selectFields . ' FROM ' . $this->getWrappedTable() . $this->joinStatement . $this->getWhereStatement() . $this->orderBy . $this->limitStatement;
        $this->union [] = $sql .' UNION ';
        $this->setTable($tableName);
        return $this;
    }

    /**
     * 联合查询，不去重
     * @param array $fields
     * @param $tableName
     * @return $this
     */
    public function selectAndUnionAll(array $fields = [],$tableName){
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $fields[ $key ] = $this->wrapName($value);
            }
            $this->selectFields = implode(',', $fields);
        } else {
            $this->selectFields = '*';
        }

        $sql = 'SELECT ' . $this->selectFields . ' FROM ' . $this->getWrappedTable() . $this->joinStatement . $this->getWhereStatement() . $this->orderBy . $this->limitStatement;
        $this->union [] = $sql .' UNION ALL ';
        $this->setTable($tableName);
        return $this;
    }

    /**
     * 追加查询条件
     * @param $statement
     * @param $and
     */
    protected function appendWhereStatement($statement,$and){
        $glue = $and ? ' AND ':' OR ';
        if(empty($this->whereStatement)){
            $this->whereStatement .= ' WHERE '.$statement;
        }else{
            if($this->hasClause){
                $this->whereStatement .= $statement;
                $this->hasClause =false;

            }else{
                $this->whereStatement .= $glue . $statement;
            }
        }
    }

    /**
     * 添加 AND查询条件
     * @param $field
     * @param $operate
     * @param $value
     * @param bool $rawValue
     * @return $this
     */
    public function where($field, $operate, $value,$rawValue = false)
    {
        $statement = $this->wrapName($field) . ' ' . $operate . ' ' . ($rawValue ? $value : $this->quote($value));
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    /**
     * 添加OR查询条件
     * @param $field
     * @param $operate
     * @param $value
     * @param bool $rawValue
     * @return $this
     */
    public function orWhere($field, $operate, $value,$rawValue = false)
    {
        $statement = $this->wrapName($field) . ' ' . $operate . ' ' . ($rawValue ? $value : $this->quote($value));
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function whereIn(string $field, array $value)
    {
        $statement = $this->wrapName($field) . ' IN (' . $this->valuesStatement($value). ')';
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    public function orWhereIn(string $field, array $value)
    {
        $statement = $this->wrapName($field) . ' IN (' . $this->valuesStatement($value) . ')';
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function whereNotIn(string $field, array $value)
    {
        $statement = $this->wrapName($field) . ' NOT IN (' . $this->valuesStatement($value) . ')';
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    public function orWhereNotIn(string $field, array $value)
    {
        $statement = $this->wrapName($field) . ' NOT IN (' . $this->valuesStatement($value) . ')';
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function whereBetween(string $field, $min, $max)
    {
        $statement = $this->wrapName($field) . ' BETWEEN ' . $this->quote($min) . ' AND ' . $this->quote($max);
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    public function orWhereBetween(string $field, $min, $max)
    {
        $statement = $this->wrapName($field) . ' BETWEEN ' . $this->quote($min) . ' AND ' . $this->quote($max);
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function whereIsNull(string $field)
    {
        $statement = $this->wrapName($field) . ' IS NULL';
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    public function orWhereIsNull(string $field)
    {
        $statement = $this->wrapName($field) . ' IS NULL';
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function whereIsNotNull(string $field)
    {
        $statement = $this->wrapName($field) . ' IS NOT NULL';
        $this->appendWhereStatement($statement,true);
        return $this;
    }

    public function orWhereIsNotNull(string $field)
    {
        $statement = $this->wrapName($field) . ' IS NOT NULL';
        $this->appendWhereStatement($statement,false);
        return $this;
    }

    public function andClause()
    {
        if (empty($this->whereStatement)) {
            $this->whereStatement .= ' WHERE (';
        } else {
            $this->whereStatement .= ' AND (';
        }
        $this->hasClause = true;
        return $this;
    }
    public function orClause()
    {
        if (empty($this->whereStatement)) {
            $this->whereStatement .= ' WHERE (';
        } else {
            $this->whereStatement .= ' OR (';
        }
        $this->hasClause = true;
        return $this;
    }

    public function closeClause()
    {
        $this->whereStatement .= ')';
        return $this;
    }

    public function increment(string $field, int $count = 1)
    {
        $statement = $this->wrapName($field) . '=' . $this->wrapName($field) . ' + ' . $count;
        if (empty($this->updateStatement)) {
            $this->updateStatement .= ' SET ' . $statement;
        } else {
            $this->updateStatement .= ',' . $statement;
        }
        return $this;
    }

    public function decrement(string $field, int $count = 1)
    {
        $statement = $this->wrapName($field) . '=' . $this->wrapName($field) . ' - ' . $count;
        if (empty($this->updateStatement)) {
            $this->updateStatement .= ' SET ' . $statement;
        } else {
            $this->updateStatement .= ',' . $statement;
        }
        return $this;
    }

    public function leftJoinAs(string $tableName, string $asTable)
    {
        $this->joinStatement .= ' LEFT JOIN ' . $this->wrapName($tableName) . ' AS ' . $this->wrapName($asTable);
        return $this;
    }

    public function innerJoinAs(string $tableName, string $asTable)
    {
        $this->joinStatement .= ' INNER JOIN ' . $this->wrapName($tableName) . ' AS ' . $this->wrapName($asTable);
        return $this;
    }

    public function rightJoinAs(string $tableName, string $asTable)
    {
        $this->joinStatement .= ' RIGHT JOIN ' . $this->wrapName($tableName) . ' AS ' . $this->wrapName($asTable);
        return $this;
    }
    public function straightJoinAs(string $tableName, string $asTable)
    {
        $this->joinStatement .= ' STRAIGHT_JOIN ' . $this->wrapName($tableName) . ' AS ' . $this->wrapName($asTable);
        return $this;
    }
    public function on($onLeftField, $onRightField){
        $this->joinStatement .= ' ON ' . $this->wrapName($onLeftField) . '=' . $this->wrapName($onRightField);
        return $this;
    }
    protected function updateStatement(array $params){
        foreach ($params as $key => $val) {
            $params[$key] = $this->wrapName($key).' = '.$this->quote($val);
        }
        return implode(',',$params);
    }
    protected function keysStatement(array $params)
    {
        $keys = [];
        foreach ($params as $key => $val) {
            $keys []= $this->wrapName($key);
        }

        return implode(',',$keys);
    }

    protected function valuesStatement(array $params)
    {
        foreach ($params as $key => $val) {
            $params[$key] = $this->quote($val);
        }
        return implode(',',$params);
    }
    protected function wrapName(string $name)
    {
        if( ! $this->enableWrapName){
            return $name;
        }
        if(empty($name) or $name === '*'){
            return $name;
        }
        //如果是:[count(*)],则去掉括号返回count(*)
        $val = ltrim($name,'[');
        if(strlen($val) !== strlen($name)){
            return rtrim($val,']');
        }
        $res = explode('.',$name);
        foreach ($res as $key=>$value){
            $res[$key] = '`' . $value . '`';
        }
        return implode('.',$res);
    }
    protected function getWhereStatement(){
        return $this->whereStatement;
    }
    protected function quote($name): string
    {
        return $this->db->quote($name);
    }

    protected function throwError($sql)
    {
        $err = $this->db->errorInfo();
        $msg = $err[2] ?? '';
        $msg .= '(SQL : ' . $sql.')';
        throw new \PDOException($msg);
    }
}