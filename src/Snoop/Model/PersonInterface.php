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
 * Represents a person.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface PersonInterface
{
    /**
     * Gets the first name.
     *
     * @return string|null
     */
    public function getFirstName();

    /**
     * Gets the last name.
     *
     * @return string|null
     */
    public function getLastName();

    /**
     * Gets the location.
     *
     * @return string|null
     */
    public function getLocation();

    /**
     * Gets the headline.
     *
     * @return string|null
     */
    public function getHeadline();

    /**
     * Gets the images of this person.
     *
     * @return array An array of urls
     */
    public function getImages();

    /**
     * Gets the jobs.
     *
     * @return JobInterface[]
     */
    public function getJobs();

    /**
     * Gets the profiles.
     *
     * @return ProfileInterface[]
     */
    public function getProfiles();
}
