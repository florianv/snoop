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
 * Represents a profile.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface ProfileInterface
{
    /**
     * Gets the profile id.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Gets the profile url.
     *
     * @return string|null
     */
    public function getUrl();

    /**
     * Gets the site name (eg. Twitter).
     *
     * @return string|null
     */
    public function getSiteName();

    /**
     * Gets the username.
     *
     * @return string|null
     */
    public function getUsername();
}
