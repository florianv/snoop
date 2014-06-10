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
 * Implementation of PersonInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Person implements PersonInterface
{
    private $firstName;
    private $lastName;
    private $location;
    private $headline;
    private $jobs;
    private $images;
    private $profiles;

    /**
     * Creates a new person.
     *
     * @param string|null        $firstName
     * @param string|null        $lastName
     * @param string|null        $location
     * @param string|null        $headline
     * @param JobInterface[]     $jobs
     * @param array              $images
     * @param ProfileInterface[] $profiles
     */
    public function __construct($firstName, $lastName, $location, $headline, array $jobs, array $images, array $profiles)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->location = $location;
        $this->headline = $headline;
        $this->jobs = $jobs;
        $this->images = $images;
        $this->profiles = $profiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->images;
    }
}
