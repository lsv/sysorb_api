<?php
namespace Lsv\SysorbApiTest;

use Lsv\SysorbApi\ErrorEntity;
use Lsv\SysorbApi\ServerEntity;

class ServerEntityTest extends \PHPUnit_Framework_TestCase
{

    public function test_serializing()
    {
        $errorslist = [
            new ErrorEntity('code1', 'message1', 'status1'),
            new ErrorEntity('code2', 'message2', 'status2'),
            new ErrorEntity('code3', 'message3', 'status3')
        ];

        $server = new ServerEntity();
        $server
            ->setName('server')
            ->setCheckinStatus(ServerEntity::OK_STATUS)
            ->setAgentStatus(ServerEntity::OK_STATUS)
            ->setNetworkStatus(ServerEntity::OK_STATUS)
            ->setErrors($errorslist)
        ;

        $serialized = serialize($server);
        /** @var ServerEntity $unserialized */
        $unserialized = unserialize($serialized);

        $this->assertEquals($server->getName(), $unserialized->getName());
        $this->assertEquals($server->getCheckinStatus(), $unserialized->getCheckinStatus());
        $this->assertEquals($server->getAgentStatus(), $unserialized->getAgentStatus());
        $this->assertEquals($server->getNetworkStatus(), $unserialized->getNetworkStatus());
        $this->assertEquals($server->getErrors(), $unserialized->getErrors());

    }

    public function test_checkinstatus_set_haserror()
    {
        $server = new ServerEntity;
        $server->setCheckinStatus(ServerEntity::ERROR_STATUS);
        $this->assertTrue($server->hasError());
    }

    public function test_agentstatus_set_haserror()
    {
        $server = new ServerEntity;
        $server->setAgentStatus(ServerEntity::ERROR_STATUS);
        $this->assertTrue($server->hasError());
    }

    public function test_networkstatus_set_haserror()
    {
        $server = new ServerEntity;
        $server->setNetworkStatus(ServerEntity::ERROR_STATUS);
        $this->assertTrue($server->hasError());
    }

}
