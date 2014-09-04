<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Tests\Adapter;

use Snoop\Adapter\Guzzle4Adapter;
use Snoop\Exception\InvalidResponseException;

class Guzzle4AdapterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Client')) {
            $this->markTestSkipped('Guzzle4 needs to be installed');
        }
    }

    /**
     * @test
     */
    public function it_sends_a_request_and_gets_the_decoded_json_body()
    {
        $headers = array('Foo' => 'Bar');
        $json = array('foo' => 'bar');
        $uri = 'uri';

        $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $request
            ->expects($this->once())
            ->method('setHeaders')
            ->with($headers);

        $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $response
            ->expects($this->once())
            ->method('json')
            ->will($this->returnValue($json));

        $client = $this->getMock('GuzzleHttp\ClientInterface');
        $client
            ->expects($this->once())
            ->method('createRequest')
            ->with('GET', $uri)
            ->will($this->returnValue($request));

        $client
            ->expects($this->once())
            ->method('send')
            ->with($request)
            ->will($this->returnValue($response));

        $adapter = new Guzzle4Adapter($client);
        $result = $adapter->getArray($uri, $headers);

        $this->assertEquals($json, $result);
    }

    /**
     * @test
     */
    public function it_throws_an_invalid_token_exception_when_receiving_a_bad_response()
    {
        $uri = 'uri';
        $bodyArray = array('foo' => 'bar');

        $request = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $request
            ->expects($this->once())
            ->method('setHeaders')
            ->with(array());

        $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(403));

        $response
            ->expects($this->once())
            ->method('json')
            ->will($this->returnValue($bodyArray));

        $exception = $this->getMockBuilder('GuzzleHttp\Exception\BadResponseException')
            ->setConstructorArgs(array('', $request))
            ->getMock();

        $exception
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $client = $this->getMock('GuzzleHttp\ClientInterface');
        $client
            ->expects($this->once())
            ->method('createRequest')
            ->with('GET', $uri)
            ->will($this->returnValue($request));

        $client
            ->expects($this->once())
            ->method('send')
            ->with($request)
            ->will($this->throwException($exception));

        $adapter = new Guzzle4Adapter($client);

        try {
            $adapter->getArray($uri);
        } catch (InvalidResponseException $e) {
            $this->assertEquals($bodyArray, $e->getBodyArray());
            $this->assertSame(403, $e->getStatusCode());
        }
    }
}
