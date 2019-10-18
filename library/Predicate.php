<?php
namespace SecureFilebase;

class Predicate
{
    protected $allowed_operators = [
        '=',
        '==',
        '===',
        '!=',
        '!==',
        '>',
        '<',
        '>=',
        '<=',
        'IN',
        'NOT'
    ];

    protected $predicates = [];


    //--------------------------------------------------------------------

    public function add($logic,$arg)
    {
        if (count($arg) != 3)
        {
            throw new \InvalidArgumentException('Predicate Error: Must have 3 arguments passed - '.count($arg).' given');
        }

        if (!in_array($arg[1], $this->allowed_operators))
        {
            throw new \InvalidArgumentException('Predicate Error: Unknown Operator '.$arg[1]);
        }

        $arg[0] = trim($arg[0]);

        if ($arg[0] == '')
        {
            throw new \InvalidArgumentException('Field name can not be empty');
        }

        $this->predicates[$logic][] = $arg;
    }

    public function get()
    {
        return array_filter($this->predicates);
    }
}
