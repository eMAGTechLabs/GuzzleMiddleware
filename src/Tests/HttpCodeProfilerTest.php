<?php

namespace EmagTechLabs\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use Liuggio\StatsdClient\Service\StatsdService;
use EmagTechLabs\GuzzleMiddleware\HttpCodeProfiler;

class HttpCodeProfilerTest extends TestCase
{
    public function testGeneratedKey(): void
    {
        $statsdService = $this->getMockBuilder(StatsdService::class)->disableOriginalConstructor()->getMock();
        $statsdService->method('increment')->will(
            $this->returnCallback(function ($arg) {
                throw new \Exception($arg);
            }));

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'])
        ]);

        $handler = HandlerStack::create($mock);
        $handler->push(new HttpCodeProfiler($statsdService));
        $client = new Client(['handler' => $handler]);

        try {
            $client->request('GET', 'www.gsp.ro/asdf');
        } catch (\Exception $exception) {
            $this->assertEquals($exception->getMessage(),  'www_gsp_ro/asdf.200');
        }
    }
}