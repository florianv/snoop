<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Exception;

/**
 * Exception thrown by a {@link Snoop\AdapterInterface} when the response has an invalid status code.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class InvalidResponseException extends AdapterException
{
    private $statusCode;
    private $bodyArray;

    /**
     * Creates a new invalid response exception.
     *
     * @param string $message
     * @param string $statusCode
     * @param array  $bodyArray
     */
    public function __construct($message, $statusCode, array $bodyArray)
    {
        parent::__construct($message);

        $this->statusCode = $statusCode;
        $this->bodyArray = $bodyArray;
    }

    /**
     * Gets the decoded JSON body.
     *
     * @return array
     */
    public function getBodyArray()
    {
        return $this->bodyArray;
    }

    /**
     * Gets the status code.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
