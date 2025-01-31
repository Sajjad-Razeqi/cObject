<?php
namespace cObject;
/**
 * <b>None</b> is a new data type for TeleBot;
 */
class None {
    public function __isset($name): bool{
        return false;
    }
    public function __call($key, $value): void{
    }
    public function __get($name): void{
    }
    public function __set($key, $value): void{
    }

    public function __tostring(): string{
        return __CLASS__;
    }
}
?>