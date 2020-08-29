<?php


namespace ZM\ConnectionManager;


use Swoole\Table;

class SharedTable
{
    /** @var null|Table */
    public static $table = null;
}
