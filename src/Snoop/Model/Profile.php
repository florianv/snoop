<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Model;

/**
 * Implementation of ProfileInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Profile implements ProfileInterface
{
    private $id;
    private $url;
    private $siteName;
    private $username;

    /**
     * Creates a new profile.
     *
     * @param string|null $id
     * @param string|null $url
     * @param string|null $siteName
     * @param string|null $username
     */
    public function __construct($id, $url, $siteName, $username)
    {
        $this->id = $id;
        $this->url = $url;
        $this->siteName = $siteName;
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getSiteName()
    {
        return $this->siteName;
    }
}
