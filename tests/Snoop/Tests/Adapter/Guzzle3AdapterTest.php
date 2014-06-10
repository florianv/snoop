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

use Snoop\Adapter\Guzzle3Adapter;
use Snoop\Exception\InvalidResponseException;

class Guzzle3AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_sends_a_request_and_gets_the_decoded_json_body()
    {
        $headers = array('Foo' => 'Bar');
        $json = array('foo' => 'bar');
        $uri = 'uri';

        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $request
            ->expects($this->once())
            ->method('setHeaders')
            ->with($headers);

        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response
            ->expects($this->once())
            ->method('json')
            ->will($this->returnValue($json));

        $client = $this->getMock('Guzzle\Http\ClientInterface');
        $client
            ->expects($this->once())
            ->method('get')
            ->with($uri)
            ->will($this->returnValue($request));

        $client
            ->expects($this->once())
            ->method('send')
            ->with($request)
            ->will($this->returnValue($response));

        $adapter = new Guzzle3Adapter($client);
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

        $request = $this->getMock('Guzzle\Http\Message\RequestInterface');
        $request
            ->expects($this->once())
            ->method('setHeaders')
            ->with(array());

        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $response
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(403));

        $response
            ->expects($this->once())
            ->method('json')
            ->will($this->returnValue($bodyArray));

        $exception = $this->getMock('Guzzle\Http\Exception\ClientErrorResponseException');
        $exception
            ->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $client = $this->getMock('Guzzle\Http\ClientInterface');
        $client
            ->expects($this->once())
            ->method('get')
            ->with($uri)
            ->will($this->returnValue($request));

        $client
            ->expects($this->once())
            ->method('send')
            ->with($request)
            ->will($this->throwException($exception));

        $adapter = new Guzzle3Adapter($client);

        try {
            $adapter->getArray($uri);
        } catch (InvalidResponseException $e) {
            $this->assertEquals($bodyArray, $e->getBodyArray());
            $this->assertSame(403, $e->getStatusCode());
        }
    }
}
