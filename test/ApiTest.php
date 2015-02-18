<?php
namespace Lsv\SysorbApiTest;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Stream\Stream;
use Lsv\SysorbApi\Api;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;
use Lsv\SysorbApi\ServerEntity;

class ApiTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ServerEntity[]
     */
    private $servers;

    protected function setUp()
    {
        $client = new Client();
        $mock = new Mock([
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/login.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
            new Response(200, [], Stream::factory(file_get_contents(__DIR__ . '/stubs/statuspage.html'))),
        ]);
        $client->getEmitter()->attach($mock);

        $api = new Api('/', 'user', 'pw', 'domain');
        $api->setClient($client);
        $api->setCookieJar(new CookieJar());
        $this->servers = $api->parse();
    }

    public function test_can_count_servers()
    {
        $this->assertEquals(20, count($this->servers));
    }

    public function test_can_get_errors_on_servers()
    {
        foreach($this->servers as $server) {
            $this->assertInstanceOf('Lsv\SysorbApi\ServerEntity', $server);
        }
    }

}
