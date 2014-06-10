<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop\Tests;

use Snoop\Snoop;

class SnoopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_generates_an_email_when_fetching_a_token_without_email()
    {
        $uri = 'https://rapportive.com/login_status?user_email=hello@world.com';
        $bodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/token_success.json'), true);

        $emailGenerator = $this->getMock('Snoop\Generator\EmailGeneratorInterface');
        $emailGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('hello@world.com'));

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->once())
            ->method('getArray')
            ->with($uri)
            ->will($this->returnValue($bodyArray));

        $snoop = new Snoop($adapter, $emailGenerator);
        $this->assertEquals('token', $snoop->fetchToken());
    }

    /**
     * @test
     */
    function it_fetches_a_token_with_email()
    {
        $uri = 'https://rapportive.com/login_status?user_email=hello@world.com';
        $bodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/token_success.json'), true);

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->once())
            ->method('getArray')
            ->with($uri)
            ->will($this->returnValue($bodyArray));

        $snoop = new Snoop($adapter);
        $this->assertEquals('token', $snoop->fetchToken('hello@world.com'));
    }

    /**
     * @test
     */
    function it_fetches_a_token_when_finding_an_email_without_token()
    {
        $tokenUri = 'https://rapportive.com/login_status?user_email=hello@world.com';
        $findUri = 'https://profiles.rapportive.com/contacts/email/foo@bar.com';
        $tokenBodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/token_success.json'), true);
        $findBodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/contact_found.json'), true);

        $emailGenerator = $this->getMock('Snoop\Generator\EmailGeneratorInterface');
        $emailGenerator
            ->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('hello@world.com'));

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->at(0))
            ->method('getArray')
            ->with($tokenUri)
            ->will($this->returnValue($tokenBodyArray));

        $adapter
            ->expects($this->at(1))
            ->method('getArray')
            ->with($findUri, array('X-Session-Token' => 'token'))
            ->will($this->returnValue($findBodyArray));

        $snoop = new Snoop($adapter, $emailGenerator);
        $person = $snoop->find('foo@bar.com');

        // Person
        $this->assertEquals('Kevin', $person->getFirstName());
        $this->assertEquals('Rose', $person->getLastName());
        $this->assertEquals('San Francisco Bay Area', $person->getLocation());
        $this->assertEquals('General Partner at Google Ventures', $person->getHeadline());

        $this->assertEquals(array(
            'http://m.c.lnkd.licdn.com/mpr/mpr/p/6/005/03b/0c1/11f1310.jpg',
            'http://a0.twimg.com/profile_images/1468433864/kr_normal.jpg',
            'http://profile.ak.fbcdn.net/hprofile-ak-snc6/274326_500014674_1887082300_n.jpg',
            'http://graph.facebook.com/6162642477/picture?type=large',
        ), $person->getImages());

        // Jobs
        $jobs = $person->getJobs();
        $firstJob = $jobs[0];
        $secondJob = $jobs[1];

        $this->assertEquals('General Partner', $firstJob->getTitle());
        $this->assertEquals('Google Ventures', $firstJob->getCompanyName());

        $this->assertEquals('Board Member', $secondJob->getTitle());
        $this->assertEquals('Tony Hawk Foundation', $secondJob->getCompanyName());

        // Profiles
        $profiles = $person->getProfiles();
        $firstProfile = $profiles[0];
        $secondProfile = $profiles[1];
        $thirdProfile = $profiles[2];
        $fourthProfile = $profiles[3];
        $this->assertCount(15, $profiles);

        $this->assertEquals('657863', $firstProfile->getId());
        $this->assertEquals('Twitter', $firstProfile->getSiteName());
        $this->assertEquals('http://twitter.com/kevinrose', $firstProfile->getUrl());
        $this->assertEquals('kevinrose', $firstProfile->getUsername());

        $this->assertEquals('500014674', $secondProfile->getId());
        $this->assertEquals('Facebook', $secondProfile->getSiteName());
        $this->assertEquals('http://www.facebook.com/kevinrose', $secondProfile->getUrl());
        $this->assertEquals('kevinrose', $secondProfile->getUsername());

        $this->assertNull($thirdProfile->getId());
        $this->assertEquals('LinkedIn', $thirdProfile->getSiteName());
        $this->assertEquals('http://www.linkedin.com/in/kevinrose', $thirdProfile->getUrl());
        $this->assertEquals('kevinrose', $thirdProfile->getUsername());

        $this->assertNull($fourthProfile->getId());
        $this->assertEquals('About.me', $fourthProfile->getSiteName());
        $this->assertEquals('http://about.me/kevinrose', $fourthProfile->getUrl());
        $this->assertEquals('kevinrose', $fourthProfile->getUsername());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function it_throws_an_exception_when_fetching_a_token_with_an_invalid_email()
    {
        $adapter = $this->getMock('Snoop\AdapterInterface');
        $snoop = new Snoop($adapter);
        $snoop->fetchToken('foobar.com');
    }

    /**
     * @test
     */
    function it_finds_and_email_with_token()
    {
        $findUri = 'https://profiles.rapportive.com/contacts/email/foo@bar.com';
        $bodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/contact_found.json'), true);

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->once())
            ->method('getArray')
            ->with($findUri, array('X-Session-Token' => 'secret'))
            ->will($this->returnValue($bodyArray));

        $snoop = new Snoop($adapter);
        $person = $snoop->find('foo@bar.com', 'secret');

        $this->assertEquals('Kevin', $person->getFirstName());
        $this->assertEquals('Rose', $person->getLastName());
        $this->assertEquals('San Francisco Bay Area', $person->getLocation());
        $this->assertEquals('General Partner at Google Ventures', $person->getHeadline());
        $this->assertCount(4, $person->getImages());
        $this->assertCount(2, $person->getJobs());
        $this->assertCount(15, $person->getProfiles());
    }

    /**
     * @test
     * @expectedException \Snoop\Exception\InvalidTokenException
     */
    function it_throws_an_invalid_token_exception_when_finding_email_with_invalid_token()
    {
        $bodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/contact_missing_token.json'), true);

        $invalidResponseException = $this->getMockBuilder('Snoop\Exception\InvalidResponseException')
            ->disableOriginalConstructor()
            ->getMock();

        $invalidResponseException
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(403));

        $invalidResponseException
            ->expects($this->once())
            ->method('getBodyArray')
            ->will($this->returnValue($bodyArray));

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->at(0))
            ->method('getArray')
            ->will($this->throwException($invalidResponseException));

        $snoop = new Snoop($adapter);
        $snoop->find('foo@bar.com', 'secret');
    }

    /**
     * @test
     * @expectedException \Snoop\Exception\PersonNotFoundException
     */
    function it_throws_a_person_not_found_exception_when_result_is_empty()
    {
        $findUri = 'https://profiles.rapportive.com/contacts/email/foo@bar.com';
        $bodyArray = json_decode(file_get_contents(__DIR__.'/../Fixtures/contact_empty.json'), true);

        $adapter = $this->getMock('Snoop\AdapterInterface');
        $adapter
            ->expects($this->once())
            ->method('getArray')
            ->with($findUri, array('X-Session-Token' => 'secret'))
            ->will($this->returnValue($bodyArray));

        $snoop = new Snoop($adapter);
        $snoop->find('foo@bar.com', 'secret');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    function it_throws_an_exception_when_trying_to_find_an_invalid_email()
    {
        $adapter = $this->getMock('Snoop\AdapterInterface');
        $snoop = new Snoop($adapter);
        $snoop->find('foobar.com');
    }
}
