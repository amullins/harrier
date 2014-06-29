<?php


class Data {
    private $store = array();

    private function Data($data = null) {
        if (!is_null($data)) $this->append($data);
    }

    public static function init($data = null) {
        return new Data($data);
    }

    private function appendArray($arr) {
        // determine if we're adding an array of KeyValue or an associative array
        if (isset($arr[0])) foreach ($arr as $kv) $this->store[$kv->key] = $kv;
        else foreach ($arr as $k => $v) $this->store[$k] = $v;
    }

    /**
     * Append a new value or values to this data.
     * @return array
     */
    public function append() {
        if (func_num_args()) {
            if (is_array(func_get_arg(0)))
                $this->appendArray(func_get_arg(0));

            else if (func_num_args() == 2 && is_string(func_get_arg(0)))
                $this->store[func_get_arg(0)] = func_get_arg(1);

            return $this->store;
        }

        return $this->store;
    }

    /**
     * Get a value by key.
     * @param string $key
     * @return mixed
     */
    public function get($key) {
        return $this->store[$key];
    }

    /**
     * Get a value by property value.
     * @param string $prop The property to match.
     * @param mixed $value The value to match.
     * @return mixed
     */
    public function get_by($prop, $value) {
        $result = array_filter($this->store, function ($v) use ($prop, $value) {
            return $v->$prop == $value;
        });
        return array_shift($result);
    }

    /**
     * Get all data.
     * @return array
     */
    public function all() {
        return $this->store;
    }

    public function __toString() {
        return print_r($this->store);
    }
}