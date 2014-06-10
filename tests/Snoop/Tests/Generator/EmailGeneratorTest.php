<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Tests\Generator;

use Snoop\Generator\EmailGenerator;

class EmailGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_generates_three_different_emails()
    {
        $generator = new EmailGenerator();

        $firstEmail = $generator->generate();
        $secondEmail = $generator->generate();
        $thirdEmail = $generator->generate();

        $this->assertNotEquals($firstEmail, $secondEmail);
        $this->assertNotEquals($firstEmail, $thirdEmail);
        $this->assertNotEquals($secondEmail, $thirdEmail);

        $this->assertValidEmail($firstEmail);
        $this->assertValidEmail($secondEmail);
        $this->assertValidEmail($thirdEmail);
    }

    private function assertValidEmail($email)
    {
        $this->assertTrue(false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}
