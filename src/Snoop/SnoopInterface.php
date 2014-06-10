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
 * Contract for the Snoop service.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface SnoopInterface
{
    /**
     * Fetches a token for the given email address.
     * If no email is given, a fake one will be generated.
     *
     * @param string|null $email
     *
     * @return string
     *
     * @throws Exception\AdapterException
     * @throws \InvalidArgumentException
     */
    public function fetchToken($email = null);

    /**
     * Finds a person's informations from an email address.
     * If no token is given, a new one will be fetched.
     *
     * @param string      $email
     * @param string|null $token
     *
     * @return Model\PersonInterface
     *
     * @throws Exception\AdapterException
     * @throws Exception\InvalidTokenException
     * @throws Exception\PersonNotFoundException
     * @throws \InvalidArgumentException
     */
    public function find($email, $token = null);
}
