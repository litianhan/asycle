<?php

namespace Asycle\Core\Database;

/**
 * Date: 2017/11/30
 * Time: 17:24
 */
class MongoHelper{
    protected $manager = null;
    protected $uri = '';
    protected $options = [];
    protected $driverOptions = [];
    public function __construct($uri, array $options = [], array $driverOptions = [])
    {
        $this->uri = $uri;
        $this->options =$options;
        $this->driverOptions = $driverOptions;
    }
    public function getManager():\MongoDB\Driver\Manager{
        if(is_null($this->manager)){
            $this->manager = new \MongoDB\Driver\Manager($this->uri,$this->options,$this->driverOptions);
        }
        return $this->manager;
    }
    public function newObjectId():\MongoDB\BSON\ObjectID{
        return new \MongoDB\BSON\ObjectID();
    }
    public function get($namespace, $filter, $options):\MongoDB\Driver\Cursor{
        $query = new \MongoDB\Driver\Query($filter, $options);
        return $this->getManager()->executeQuery($namespace, $query);
    }
    public function insertOne(string $namespace,$document,$timeout = 3000):\MongoDB\Driver\WriteResult{
        $bulk = new \MongoDB\Driver\BulkWrite();
        if( ! isset($document['_id'])){
            $document['_id'] = $this->newObjectId();
        }
        $bulk->insert($document);
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, $timeout);
        return $this->getManager()->executeBulkWrite($namespace, $bulk, $writeConcern);
    }
    public function insertBatch(string $namespace,array $documents,$timeout = 3000):\MongoDB\Driver\WriteResult{
        $bulk = new \MongoDB\Driver\BulkWrite();
        foreach ($documents as $document){
            if( ! isset($document['_id'])){
                $document['_id'] = $this->newObjectId();
            }
            $bulk->insert($document);
        }
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, $timeout);
        return $this->getManager()->executeBulkWrite($namespace, $bulk, $writeConcern);
    }
    public function update(string $namespace,$filter, $newObj, array $updateOptions = [],$timeout = 3000){
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->update(
            $filter,
            $newObj,
            $updateOptions
        );
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, $timeout);
        return $this->getManager()->executeBulkWrite($namespace, $bulk, $writeConcern);
    }
    public function delete(string $namespace,$filter, array $deleteOptions = [],$timeout = 3000){
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->delete($filter, $deleteOptions);
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, $timeout);
        return $this->getManager()->executeBulkWrite($namespace, $bulk, $writeConcern);
    }
    public function findAndModify(){

    }
    public function createIndex(array $index){
        ;
    }
    public function close(){
        $this->manager = null;
    }
}