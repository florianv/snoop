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
 * Implementation of EmailGeneratorInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class EmailGenerator implements EmailGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $extensions = array('com', 'net', 'org', 'io');

        return sprintf(
            '%s@%s.%s',
            substr(md5(uniqid()), 0, rand(5, 12)),
            substr(md5(uniqid()), 0, rand(3, 8)),
            $extensions[array_rand($extensions)]
        );
    }
}
