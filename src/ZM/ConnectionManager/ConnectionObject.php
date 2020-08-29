<?php


namespace ZM\ConnectionManager;


class ConnectionObject
{
    private $fd;
    private $name;
    private $options;

    public function __construct($fd, $obj) {
        $this->fd = intval($fd);
        $this->name = $obj["name"];
        $this->options = $obj;
    }

    /**
     * @return int
     */
    public function getFd(): int {
        return $this->fd;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getOptions() {
        return $this->options;
    }

    public function getOption($key) {
        return $this->options[$key] ?? null;
    }

    public function setName(string $name) {
        return SharedTable::$table->set(strval($this->fd), ['name' => $name]);
    }

    public function setOption($key, $value) {
        return SharedTable::$table->set(strval($this->fd), [$key => $value]);
    }
}
