<?php


use PHPUnit\Framework\TestCase;
use ZM\ConnectionManager\ManagerGM;

class ManagerGMTest extends TestCase
{
    public function testLowMemInit() {
        $this->assertEquals(true, ManagerGM::init(2));
        $this->assertEquals(true, ManagerGM::pushConnect(0));
        $this->assertEquals(true, ManagerGM::setName(0, "haha-0"));
        $this->assertEquals("haha-0", ManagerGM::get(0)->getName());
    }
}
