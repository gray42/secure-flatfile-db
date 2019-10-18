<?php

namespace SecureFilebase;

class Database
{
    protected $config;


    //--------------------------------------------------------------------

    public function __construct(array $config)
    {
        $this->config = new Config($config);

        // Check directory and create it if it doesn't exist
        if (!is_dir($this->config->dir)) {
            if (!@mkdir($this->config->dir, 0777, true)) {
                throw new \Exception(sprintf('`%s` doesn\'t exist and can\'t be created.', $this->config->dir));
            }
        } else if (!is_writable($this->config->dir)) {
            throw new \Exception(sprintf('`%s` is not writable.', $this->config->dir));
        }
    }

    public function findAll($include_documents = true, $data_only = false)
    {
        $file_extension = $this->config->format::getFileExtension();
        $file_location  = $this->config->dir . '/';

        $all_items = Filesystem::getAllFiles($file_location, $file_extension);
        if ($include_documents == true) {
            $items = [];

            foreach ($all_items as $a) {
                if ($data_only === true) {
                    $items[] = $this->get($a)->getData();
                } else {
                    $items[] = $this->get($a);
                }
            }

            return $items;
        }

        return $all_items;
    }

    public function get($id)
    {
        $content = $this->read($id);

        $document = new Document($this);
        $document->setId($id);

        if ($content) {
            if (isset($content['__created_at'])) $document->setCreatedAt($content['__created_at']);
            if (isset($content['__updated_at'])) $document->setUpdatedAt($content['__updated_at']);

            $this->set($document, (isset($content['data']) ? $content['data'] : []));
        }

        return $document;
    }

    public function set(Document $document, $data)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) $value = (array) $value;
                $document->{$key} = $value;
            }
        }

        return $document;
    }

    public function count()
    {
        return count($this->findAll(false));
    }

    public function save(Document $document, $wdata = '')
    {
        $id             = $document->getId();
        $file_extension = $this->config->format::getFileExtension();
        $file_location  = $this->config->dir . '/' . Filesystem::validateName($id) . '.' . $file_extension;
        $created        = $document->createdAt(false);

        if (isset($wdata) && $wdata !== '') {
            $document = new Document($this);
            $document->setId($id);
            $document->set($wdata);
            $document->setCreatedAt($created);
        }

        if (!Filesystem::read($file_location) || $created == false) {
            $document->setCreatedAt(time());
        }

        $document->setUpdatedAt(time());

        $data = $this->config->format::encode($document->saveAs(), $this->config->pretty);

        if (Filesystem::write($file_location, $data)) {
            return $document;
        } else {
            return false;
        }
    }

    public function query()
    {
        return new Query($this);
    }
    protected function read($name)
    {
        return $this->config->format::decode(Filesystem::read($this->config->dir . '/' . Filesystem::validateName($name) . '.' . $this->config->format::getFileExtension()));
    }
    public function delete(Document $document)
    {
        return Filesystem::delete($this->config->dir . '/' . Filesystem::validateName($document->getId()) . '.' . $this->config->format::getFileExtension());
    }

    public function flush($confirm = false)
    {
        if ($confirm === true) {
            $documents = $this->findAll(false);
            foreach ($documents as $document) {
                Filesystem::delete($this->config->dir . '/' . $document . '.' . $this->config->format::getFileExtension());
            }

            if ($this->count() === 0) {
                return true;
            } else {
                throw new \Exception("Could not delete all database files in " . $this->config->dir);
            }
        } else {
            throw new \Exception("Database Flush failed. You must send in TRUE to confirm action.");
        }
    }

    public function flushCache()
    {
        $cache = new Cache($this);
        $cache->flush();
    }

    public function toArray(Document $document)
    {
        return $this->objectToArray($document->getData());
    }

    public function objectToArray($obj)
    {
        if (!is_object($obj) && !is_array($obj)) {
            return $obj;
        }

        $arr = [];
        foreach ($obj as $key => $value) {
            $arr[$key] = $this->objectToArray($value);
        }

        return $arr;
    }
    public function getConfig()
    {
        return $this->config;
    }
}