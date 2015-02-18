<?php
namespace Lsv\SysorbApiTest;

use Lsv\SysorbApi\ErrorEntity;

class ErrorEntityTest extends \PHPUnit_Framework_TestCase
{

    public function test_serializing()
    {
        $obj = new ErrorEntity();
        $obj
            ->setCode('code')
            ->setStatus('status')
            ->setMessage('message')
        ;

        $serialized = serialize($obj);
        $newobj = unserialize($serialized);

        $this->assertEquals($obj->getCode(), $newobj->getCode());
        $this->assertEquals($obj->getStatus(), $newobj->getStatus());
        $this->assertEquals($obj->getMessage(), $newobj->getMessage());

    }

}
