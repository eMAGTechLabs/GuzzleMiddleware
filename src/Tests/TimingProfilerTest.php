<?php
namespace EmagTechLabs\Tests;


use EmagTechLabs\GuzzleMiddleware\Adaptor\StatsDataInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use EmagTechLabs\GuzzleMiddleware\TimingProfiler;


class TimingProfilerTest extends TestCase
{

    public function testGeneratedKey(): void
    {
        $statsdService = $this->getMockBuilder(StatsDataInterface::class)->disableOriginalConstructor()->getMock();
        $statsdService->method('timing')->will(
            $this->returnCallback(function($arg) {
                throw new \Exception($arg);
            }));

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'])
        ]);

        $handler = HandlerStack::create($mock);
        $handler->push(new TimingProfiler($statsdService));
        $client = new Client(['handler' => $handler]);

        try {
            $client->request('GET', 'www.gsp.ro/asdf');
        } catch (\Exception $exception) {
            $this->assertEquals($exception->getMessage(), 'www_gsp_ro/asdf');
        }
    }
}
