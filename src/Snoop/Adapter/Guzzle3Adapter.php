<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Adapter;

use Guzzle\Http\Exception\BadResponseException;
use Snoop\Exception\InvalidResponseException;
use Snoop\Exception\AdapterException;
use Guzzle\Http\ClientInterface;
use Snoop\AdapterInterface;

/**
 * Adapter for Guzzle 3.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Guzzle3Adapter implements AdapterInterface
{
    private $client;

    /**
     * Creates a new adapter.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getArray($uri, array $headers = array())
    {
        $request = $this->client->get($uri);
        $request->setHeaders($headers);

        try {
            $response = $this->client->send($request);
        } catch (\Exception $exception) {
            if ($exception instanceof BadResponseException) {
                $response = $exception->getResponse();
                $statusCode = $response->getStatusCode();

                try {
                    $bodyArray = $response->json();
                } catch (\Exception $e) {
                    $bodyArray = array();
                }

                throw new InvalidResponseException($exception->getMessage(), $statusCode, $bodyArray);
            }

            throw new AdapterException($exception->getMessage());
        }

        return $response->json();
    }
}
