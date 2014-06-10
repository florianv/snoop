<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop;

/**
 * Contract for clients adapters.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface AdapterInterface
{
    /**
     * Performs a GET request and returns the decoded JSON response body.
     *
     * @param string $uri
     * @param array  $headers
     *
     * @return array
     *
     * @throws Exception\AdapterException
     * @throws Exception\InvalidResponseException
     */
    public function getArray($uri, array $headers = array());
}
