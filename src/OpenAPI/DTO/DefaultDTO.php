<?php
namespace OpenAPI\DTO;

class DefaultDTO {
    //All properties are sent here if not declared
    private $allData = [];

    //Is $allData allowed to be used
    private $strict = false;

    function __construct(array $data = null)
    {
        if ($data !== null) {
            $this->fill($data);
        }
    }

    function __get($key) {
        if (!isset($this->{$key}) && !isset($this->allData[$key])) {
            throw new \Exception("Unknown key " . $key);
        }
        if ($this->strict && !isset($this->{$key})) {
            throw new \Exception("Unknown defined key " . $key);
        }
        return isset($this->{$key}) ? $this->{$key} : $this->allData[$key];
    }

    function __set($key, $value) {
        if (!isset($this->{$key}) && $this->strict) {
            throw new \Exception("Unknown defined key " . $key);
        }
        if (!isset($this->{$key})) {
            $this->allData[$key] = $value;
            return;
        }
        $this->{$key} = $value;
    }

    public function fill(array $data) {
        foreach($data as $key => $row) {
            $this->__set($key, $row);
        }
    }
}