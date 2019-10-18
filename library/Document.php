<?php

namespace SecureFilebase;

class Document
{

    private $__database;
    private $__id;
    private $__created_at;
    private $__updated_at;
    private $__cache = false;
    private $data = [];

    public function __construct($database)
    {
        $this->__database = $database;
    }
    public function saveAs()
    {
        $data = (object) [];
        $vars = get_object_vars($this);

        foreach ($vars as $k => $v) {
            if (in_array($k, ['__database', '__id', '__cache'])) continue;
            $data->{$k} = $v;
        }
        return $data;
    }
    public function save($data = '')
    {
        Validate::valid($this);

        return $this->__database->save($this, $data);
    }
    public function delete()
    {
        return $this->__database->delete($this);
    }
    public function set($data)
    {
        return $this->__database->set($this, $data);
    }
    public function toArray()
    {
        return $this->__database->toArray($this);
    }
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    public function &__get($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = null;
        }

        return $this->data[$name];
    }
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
    public function __unset($name)
    {
        unset($this->data[$name]);
    }
    public function customFilter(string $field, callable $function)
    {
        $items = $this->field($field);

        if (!is_array($items) || empty($items)) {
            return [];
        }

        $r = [];
        foreach ($items as $index => $item) {
            $i = $function($item);
            if ($i !== false || is_null($i)) {
                $r[$index] = $function($item);
            }
        }

        return $r;
    }
    public function getDatabase()
    {
        return $this->__database;
    }
    public function getId()
    {
        return $this->__id;
    }
    public function getData()
    {
        return $this->data;
    }
    public function setId($id)
    {
        $this->__id = $id;

        return $this;
    }

    public function setFromCache(bool $cache = true)
    {
        $this->__cache = $cache;

        return $this;
    }

    public function isCache()
    {
        return $this->__cache;
    }
    public function createdAt($format = 'Y-m-d H:i:s')
    {
        if (!$this->__created_at) {
            return date($format);
        }

        if ($format !== false) {
            return date($format, $this->__created_at);
        }

        return $this->__created_at;
    }
    public function updatedAt($format = 'Y-m-d H:i:s')
    {
        if (!$this->__updated_at) {
            return date($format);
        }

        if ($format !== false) {
            return date($format, $this->__updated_at);
        }

        return $this->__updated_at;
    }
    public function setCreatedAt($created_at)
    {
        $this->__created_at = $created_at;

        return $this;
    }
    public function setUpdatedAt($updated_at)
    {
        $this->__updated_at = $updated_at;

        return $this;
    }
    public function field($field)
    {
        $parts   = explode('.', $field);
        $context = $this->data;

        if ($field == 'data') {
            return $context;
        }

        foreach ($parts as $part) {
            if (trim($part) == '') {
                return false;
            }

            if (is_object($context)) {
                if (!property_exists($context, $part)) {
                    return false;
                }

                $context = $context->{$part};
            } else if (is_array($context)) {
                if (!array_key_exists($part, $context)) {
                    return false;
                }

                $context = $context[$part];
            }
        }

        return $context;
    }
}
