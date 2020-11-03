<?php


namespace ZM\ConnectionManager;


class ConnectionObject
{
    private $fd;
    private $str_fd;

    public function __construct($fd) {
        $this->fd = intval($fd);
        $this->str_fd = strval($fd);
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
        return SharedTable::$table->get($this->str_fd, 'name');
    }

    /**
     * @return mixed
     */
    public function getOptions() {
        return SharedTable::$table->get($this->str_fd);
    }

    public function getOption($key) {
        return $this->getOptions()[$key] ?? null;
    }

    public function setName(string $name) {
        return SharedTable::$table->set($this->str_fd, ['name' => $name]);
    }

    public function setOption($key, $value) {
        return SharedTable::$table->set($this->str_fd, [$key => $value]);
    }
}
