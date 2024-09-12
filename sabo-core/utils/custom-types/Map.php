<?php

namespace SaboCore\Utils\CustomTypes;

use Closure;

/**
 * @brief represent a map
 * @template MapOf type of contained elements
 */
class Map{
    /**
     * @param array $map map content
     */
    public function __construct(protected array $map = []){
    }

    /**
     * @param string $key element key in map
     * @return MapOf linked element in map or null if not found
     */
    public function get(string $key):mixed{
        return $this->map[$key] ?? null;
    }

    /**
     * @brief insert a value in the map
     * @param string $key element key
     * @param MapOf $value element value
     * @param bool $override if true override the key value if already exists
     * @return $this
     */
    public function set(string $key,mixed $value,bool $override = true):static{
        if(!$override && $this->haveKey(key: $key))
            return $this;

        $this->map[$key] = $value;

        return $this;
    }

    /**
     * @param string $key key
     * @return bool if the map contain the given key
     */
    public function haveKey(string $key):bool{
        return array_key_exists(key: $key,array: $this->map);
    }

    /**
     * @brief search the keys which are linked to the given value
     * @param MapOf $searchedValue searched element
     * @param Closure|null $comparator comparator function with the format fn(mixed one,mixed two):bool => ..., by default elements are compared using equal
     * @return string[] founded keys
     */
    public function keysFromValue(mixed $searchedValue,?Closure $comparator = null):array{
        $keys = [];

        if($comparator === null)
            $comparator = fn(mixed $one,mixed $two):bool => $one === $two;

        foreach($this->map as $key => $value){
            if($comparator($value,$searchedValue))
                $keys[] = $key;
        }

        return $keys;
    }

    /**
     * @return array array version of the map
     */
    public function toArray():array{
        return $this->map;
    }
}