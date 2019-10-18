<?php

namespace SecureFilebase;


class Config
{
    public $dir = __DIR__;
    public $format = Format\Json::class;
    public $cache = false;
    public $cache_expires = 0; /*0seconds*/
    public $pretty = true;
    public $validate = [];


    //--------------------------------------------------------------------

    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }

        $this->validateFormatClass();
    }
    protected function validateFormatClass()
    {
        if (!class_exists($this->format)) {
            throw new \Exception('SecureFilebase Error: Missing format class in config.');
        }

        $format_class = new $this->format;

        if (!$format_class instanceof Format\FormatInterface) {
            throw new \Exception('SecureFilebase Error: Format Class must be an instance of SecureFilebase\Format\FormatInterface');
        }
    }
}
