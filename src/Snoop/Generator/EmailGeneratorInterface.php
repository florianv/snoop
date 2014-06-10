<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Generator;

/**
 * Contract for email generators.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
interface EmailGeneratorInterface
{
    /**
     * Generates a fake email address.
     *
     * @return string
     */
    public function generate();
}
