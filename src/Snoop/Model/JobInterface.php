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
 * Represents a job.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface JobInterface
{
    /**
     * Gets the job title.
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Gets the company name.
     *
     * @return string|null
     */
    public function getCompanyName();
}
