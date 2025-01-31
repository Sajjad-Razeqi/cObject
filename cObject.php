<?php

namespace cObject;

use cObject\None;

#[\AllowDynamicProperties]

/**
 * <b>cObject</b> is a stdclass replacement class for callable objects and normal objects.
 */
class cObject implements \ArrayAccess
{
    /**
     * @var array $storedData Stores all data added to the class.
     */
    private array $storedData = [];

    /**
     * @var array $storedArrayData Stores all data as Array.
     */
    private array $storedArrayData = [];

    /**
     * @param array $data Get first data to convert to cObject.
     */
    public function __construct(array $data = [])
    {
        if (array_keys($data) === range(0, count($data) - 1)) {
            $createArray = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $getAll = new cObject($value);
                    $createArray[$key] = $getAll->cObjectGetAll();
                } else {
                    $createArray[$key] = $value;
                }
            }
            $this->storedData = (array) $createArray;
        } else {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $getAll = new cObject($value);
                    $this->storedData[$key] = $getAll->cObjectGetAll();
                } else {
                    $this->storedData[$key] = $value;
                }
            }
        }
        $this->storedArrayData = self::cObjectToArray($this);
    }

    /**
     * @param string $name The name of callabe variable.
     * @param array $arguments The arguments of callabe variable.
     *
     * @return mixed The output of callabe variable.
     * 
     * @throws BadFunctionCallException If The Method does not exists.
     * @throws BadMethodCallException If The Method is not callable.
     */
    public function __call(string $name, array $arguments): mixed
    {
        $callable = null;
        if (array_key_exists($name,$this->storedData)) {
            $callable = $this->storedData[$name];
            if (!is_callable($callable)) {
                throw new \BadMethodCallException("Method {$name} is not callable.");
            }
            return call_user_func_array($callable, $arguments);
        }
        throw new \BadFunctionCallException("Method {$name} does not exists.");
    }
    /**
     * @param string $name The name of property.

     * @return bool The output of __isset.
     */
    public function __isset($name): bool
    {
        return array_key_exists($name,$this->storedData);
    }
    /**
     * @param string $name The name of property.
     * @param mixed $value The value of property.
     *
     * @return void The output of __set.
     */
    public function __set(string $name, mixed $value): void
    {
        if (is_array($value)) {
            $getAll = new cObject($value);
            $this->storedData[$name] = $getAll->cObjectGetAll();
        } else {
            $this->storedData[$name] = $value;
        }
        $this->storedArrayData = self::cObjectToArray($this);
    }
    /**
     * @param string $name The name of property.
     *
     * @return void The output of __unset.
     */
    public function __unset(string $name): void
    {
        unset($this->storedData[$name]);
        $this->storedArrayData = self::cObjectToArray($this);
    }

    /**
     * @param string $name The name of property.
     *
     * @return mixed The output of __get.
     * 
     * @throws \OutOfBoundsException If The Property or Method does not exists.
     */
    public function &__get(string $name): mixed
    {
        if (array_key_exists($name,$this->storedData)) {
            return $this->storedData[$name];
        }
        throw new \OutOfBoundsException("Property or Method {$name} does not exists.");
    }

    /**
     * 
     * @return string The output is json.
     * 
     */
    public function __toString(): string
    {
        $Array = ["_" => "cObject", ...$this->MycObjectToArray()];
        return json_encode($Array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // <=================================================>


    /**
     * @return array|cObject The output of cObjectGetAll.
     * 
     */
    public function cObjectGetAll(): array|cObject
    {
        if (array_keys($this->storedData) === range(0, count($this->storedData) - 1)) {
            return $this->storedData;
        } else {
            return $this;
        }
    }

    /**
     * @param cObject $cObject The object that convert to Array.
     *
     * @return array The output of cObjectToArray.
     */
    public static function cObjectToArray(cObject $cObject): array
    {
        $toArray = [];
        foreach ($cObject->cObjectForceGet() as $key => $value) {
            if ($value instanceof cObject) {
                $value = self::cObjectToArray($value);
            }
            $toArray[$key] = $value;
        }
        return $toArray;
    }

    /**
     * @param bool $usageAlready The use of data already exists.
     * 
     * @return array The output of MycObjectToArray.
     */
    public function MycObjectToArray(bool $usageAlready = true): array
    {
        if (!empty($this->storedArrayData) and $usageAlready) {
            return $this->storedArrayData;
        } else {
            $toArray = [];
            foreach ($this->cObjectForceGet() as $key => $value) {
                if ($value instanceof cObject) {
                    $value = self::cObjectToArray($value);
                }
                $toArray[$key] = $value;
            }
            $this->storedArrayData = $toArray;
            return $toArray;
        }
    }

    /**
     * @param string $name The name of property.
     * @param mixed $newValue value The value of property.
     *
     * @return mixed The output of property.
     * 
     * @throws \OutOfBoundsException If The Property or Method does not exists.
     */
    public function cObjectForceGet(int|string|null $name = Null, mixed $newValue = new None): mixed
    {
        if (isset($name)) {
            if (!($newValue instanceof None)) {
                $this->storedData[$name] = $newValue;
                $this->storedArrayData = self::cObjectToArray($this);
                return $this->storedData;
            } else {
                if (array_key_exists($name,$this->storedData)) {
                    return $this->storedData[$name];
                }
                throw new \OutOfBoundsException("Property or Method {$name} does not exists.");
            }
        } else {
            return $this->storedData;
        }
    }

    /**
     * @param string $name The name of property.
     * @param mixed $value The value of property.
     *
     * @return mixed The output of cObjectSetnew.
     */
    public function cObjectSetnew(string $name, mixed $value = []): mixed
    {
        if (is_array($value)) {
            $getAll = new cObject($value);
            $this->storedData[$name] = $getAll->cObjectGetAll();
        } else {
            $this->storedData[$name] = $value;
        }
        $this->storedArrayData = self::cObjectToArray($this);
        return $this->storedData[$name];
    }


    // <=================================================>

    /**
     * @param string|int $offset The name of property.
     * @param mixed $value The value of property.
     *
     * @return void The output of offsetSet.
     */
    public function offsetSet($offset, $value): void
    {
        if (is_array($value)) {
            $getAll = new cObject($value);
            $this->storedData[$offset] = $getAll->cObjectGetAll();
        } else {
            $this->storedData[$offset] = $value;
        }
        $this->storedArrayData = self::cObjectToArray($this);
    }

    /**
     * @param string|int $offset The name of property.
     *
     * @return void The output of offsetUnset.
     */
    public function offsetUnset($offset): void
    {
        unset($this->storedData[$offset]);
        $this->storedArrayData = self::cObjectToArray($this);
    }

    /**
     * @param string|int $offset The name of property.
     *
     * @return mixed The output of offsetGet.
     * 
     * @throws \OutOfBoundsException If The Property or Method does not exists.
     */
    public function offsetGet($offset): mixed
    {
        if (array_key_exists($offset,$this->storedData)) {
            return $this->storedData[$offset];
        }
        throw new \OutOfBoundsException("Property or Method {$offset} does not exists.");
    }

    /**
     * @param string|int $offset The name of property.

     * @return bool The output of offsetExists.
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset,$this->storedData);
    }
}
