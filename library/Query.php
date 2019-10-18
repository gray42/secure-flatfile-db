<?php

namespace SecureFilebase;

class Query extends QueryLogic
{
    protected $documents = [];

    //--------------------------------------------------------------------

    public function where(...$arg)
    {
        $this->addPredicate('and', $arg);

        return $this;
    }
    public function andWhere(...$arg)
    {
        $this->addPredicate('and', $arg);

        return $this;
    }
    public function orWhere(...$arg)
    {
        $this->addPredicate('or', $arg);

        return $this;
    }
    protected function addPredicate($logic, $arg)
    {
        if (count($arg) == 3) {
            $this->predicate->add($logic, $arg);
        }

        if (count($arg) == 1) {
            if (isset($arg[0]) && count($arg[0])) {
                foreach ($arg[0] as $key => $value) {
                    if ($value == '') continue;

                    $this->predicate->add($logic, $this->formatWhere($key, $value));
                }
            }
        }
    }
    protected function formatWhere($key, $value)
    {
        return [$key, '==', $value];
    }
    public function getDocuments()
    {
        return $this->documents;
    }
    public function results()
    {
        return parent::run()->toArray();
    }
    public function resultDocuments()
    {
        return parent::run()->getDocuments();
    }
    public function toArray()
    {
        $docs = [];

        if (!empty($this->documents)) {
            foreach ($this->documents as $document) {
                $docs[] = (array) $document->getData();
            }
        }

        return $docs;
    }
}
