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
 * Implementation of JobInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Job implements JobInterface
{
    private $title;
    private $companyName;

    /**
     * Creates a new job.
     *
     * @param string $title
     * @param string $companyName
     */
    public function __construct($title, $companyName)
    {
        $this->title = $title;
        $this->companyName = $companyName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }
}
