<?php

namespace SecureFilebase;

class Cache
{
    protected $database;
    protected $cache_database;
    protected $key;

    //--------------------------------------------------------------------

    public function __construct(Database $database)
    {
        $this->database = $database;

        $this->cache_database  = new \SecureFilebase\Database([
            'dir' => $this->database->getConfig()->dir . '/__cache',
            'cache' => false,
            'pretty' => false
        ]);
    }
    public function setKey($key)
    {
        $this->key = md5($key);
    }
    public function getKey()
    {
        return $this->key;
    }
    public function flush()
    {
        $this->cache_database->flush(true);
    }
    public function expired($time)
    {
        if ((strtotime($time) + $this->database->getConfig()->cache_expires) > time()) {
            return false;
        }

        return true;
    }
    public function getDocuments($documents)
    {
        $d = [];
        foreach ($documents as $document) {
            $d[] = $this->database->get($document)->setFromCache(true);
        }

        return $d;
    }
    public function get()
    {
        if (!$this->getKey()) {
            throw new \Exception('You must supply a cache key using setKey to get cache data.');
        }

        $cache_doc = $this->cache_database->get($this->getKey());

        if (!$cache_doc->toArray()) {
            return false;
        }

        if ($this->expired($cache_doc->updatedAt())) {
            return false;
        }

        return $this->getDocuments($cache_doc->toArray());
    }

    public function store($data)
    {
        if (!$this->getKey()) {
            throw new \Exception('You must supply a cache key using setKey to store cache data.');
        }
        return $this->cache_database->get($this->getKey())->set($data)->save();
    }
}
