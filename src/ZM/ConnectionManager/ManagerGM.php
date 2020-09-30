<?php


namespace ZM\ConnectionManager;

use Swoole\Table;

class ManagerGM
{
    /**
     * @param $max_conn
     * @param float $conflict_proportion
     * @param array $options
     * @return mixed
     * @throws TableException
     */
    public static function init($max_conn, $conflict_proportion = 0.2, $options = [])
    {
        SharedTable::$table = new Table($max_conn, $conflict_proportion);
        SharedTable::$table->column("name", Table::TYPE_STRING, 128);
        foreach ($options as $v) {
            self::setColumn($v);
        }
        return SharedTable::$table->create();
    }

    /**
     * 插入一条连接
     * @param $fd
     * @param string $name
     * @param array $options
     * @return mixed
     */
    public static function pushConnect($fd, $name = 'default', $options = [])
    {
        $options["name"] = $name;
        return SharedTable::$table->set(strval($fd), $options);
    }

    /**
     * @param $fd
     * @param string $name
     * @return mixed
     */
    public static function setName($fd, string $name)
    {
        if (self::get($fd) === null) return false;
        return SharedTable::$table->set(strval($fd), ['name' => $name]);
    }

    public static function setOption($fd, $key, $value)
    {
        return SharedTable::$table->set(strval($fd), [$key => $value]);
    }

    /**
     * @param $fd
     * @return ConnectionObject|null
     */
    public static function get($fd)
    {
        $r = SharedTable::$table->get(strval($fd));
        return $r !== false ? new ConnectionObject($fd, $r) : null;
    }

    /**
     * @param string $name
     * @return ConnectionObject[]
     */
    public static function getAllByName($name = "default")
    {
        $obj = [];
        foreach (SharedTable::$table as $k => $v) {
            if ($v["name"] == $name) {
                $obj[] = new ConnectionObject($k, $v);
            }
        }
        return $obj;
    }

    /**
     * @param $fd
     * @return mixed
     */
    public static function popConnect($fd)
    {
        return SharedTable::$table->del(strval($fd));
    }

    /**
     * @param $column
     * @throws TableException
     */
    private static function setColumn($column)
    {
        if (!isset($column["type"], $column["key"])) throw new TableException("Column parameter required: [type, key]");
        if (in_array($column["key"], ['name', 'fd'])) throw new TableException("Column key cannot be set as \"{$column["key"]}\"");
        switch (strtolower($column["type"])) {
            case "int4":
            case "int":
                SharedTable::$table->column($column["key"], Table::TYPE_INT);
                break;
            case "int1":
                SharedTable::$table->column($column["key"], Table::TYPE_INT, 1);
                break;
            case "int2":
                SharedTable::$table->column($column["key"], Table::TYPE_INT, 2);
                break;
            case "int8":
                SharedTable::$table->column($column["key"], Table::TYPE_INT, 8);
                break;
            case "float":
                SharedTable::$table->column($column["key"], Table::TYPE_FLOAT);
                break;
            case "string":
                if (!isset($column["size"])) throw new TableException("Column parameter required: [type, key, size]");
                SharedTable::$table->column($column["key"], Table::TYPE_STRING, $column["size"]);
                break;
        }
    }

    public static function getTypeClassName(string $type)
    {
        $map = [
            "qq" => "qq",
            "universal" => "qq",
            "webconsole" => "webconsole",
            "proxy" => "proxy",
            "terminal" => "terminal",
            "" => "default"
        ];
        return $map[$type] ?? $type;
    }
}
