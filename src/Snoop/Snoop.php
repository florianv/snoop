<?php

/*
 * This file is part of Snoop.
 *
 * (c) Florian Voutzinos <florian@voutzinos.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snoop;

use Snoop\Exception\InvalidResponseException;
use Snoop\Exception\InvalidTokenException;
use Snoop\Exception\PersonNotFoundException;
use Snoop\Generator\EmailGeneratorInterface;
use Snoop\Generator\EmailGenerator;
use Snoop\Model\PersonInterface;
use Snoop\Model\Profile;
use Snoop\Model\Person;
use Snoop\Model\Job;

/**
 * Implementation of SnoopInterface.
 *
 * @author Florian Voutzinos <florian@voutzinos.com>
 */
class Snoop implements SnoopInterface
{
    const TOKEN_URI = 'https://rapportive.com/login_status?user_email=%s';
    const FIND_URI = 'https://profiles.rapportive.com/contacts/email/%s';

    private $adapter;
    private $emailGenerator;

    /**
     * Creates a new Snoop instance.
     *
     * @param AdapterInterface        $adapter
     * @param EmailGeneratorInterface $emailGenerator
     */
    public function __construct(AdapterInterface $adapter, EmailGeneratorInterface $emailGenerator = null)
    {
        $this->adapter = $adapter;
        $this->emailGenerator = $emailGenerator ?: new EmailGenerator();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchToken($email = null)
    {
        if (null === $email) {
            $email = $this->emailGenerator->generate();
        } elseif (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not a valid email address.',
                $email
            ));
        }

        $array = $this->adapter->getArray(sprintf(self::TOKEN_URI, $email));

        return $array['session_token'];
    }

    /**
     * {@inheritdoc}
     */
    public function find($email, $token = null)
    {
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not a valid email address.',
                $email
            ));
        }

        if (null === $token) {
            $token = $this->fetchToken();
        }

        try {
            $array = $this->adapter->getArray(
                sprintf(self::FIND_URI, $email),
                array('X-Session-Token' => $token)
            );
        } catch (InvalidResponseException $exception) {
            if (403 === $exception->getStatusCode()) {
                $bodyArray = $exception->getBodyArray();

                if (isset($bodyArray['error_code']) && 'missing_session_token' === $bodyArray['error_code']) {
                    throw new InvalidTokenException();
                }
            }

            throw $exception;
        }

        $person = $this->createPerson($array['contact']);

        $this->validatePerson($person, $email);

        return $person;
    }

    /**
     * Creates a person instance from the contact informations.
     *
     * @param array $contact
     *
     * @return Model\PersonInterface
     */
    private function createPerson(array $contact)
    {
        $firstName = $this->getNonEmptyValue($contact, 'first_name');
        $lastName = $this->getNonEmptyValue($contact, 'last_name');
        $location = $this->getNonEmptyValue($contact, 'location');
        $headline = $this->getNonEmptyValue($contact, 'headline');

        $jobs = array();
        foreach ($contact['occupations'] as $occupation) {
            $jobs[$occupation['company']] = new Job(
                $this->getNonEmptyValue($occupation, 'job_title'),
                $this->getNonEmptyValue($occupation, 'company')
            );
        }

        $profiles = array();
        foreach ($contact['memberships'] as $membership) {
            $displayName = $membership['display_name'];

            // Sometimes profiles are duplicated with a null username
            if (!isset($profiles[$displayName]) || null !== $membership['username']) {
                $profiles[$displayName] = new Profile(
                    $this->getNonEmptyValue($membership, 'profile_id'),
                    $this->getNonEmptyValue($membership, 'profile_url'),
                    $this->getNonEmptyValue($membership, 'site_name'),
                    $this->getNonEmptyValue($membership, 'username')
                );
            }
        }

        $images = array();
        foreach ($contact['images'] as $image) {
            $url = $image['url'];

            // Some wrong images get inserted
            if (false !== strpos($url, 'd=404')) {
                continue;
            }

            $images[] = $url;
        }

        return new Person($firstName, $lastName, $location, $headline, array_values($jobs), $images, array_values($profiles));
    }

    /**
     * Throws a not found exception if all fields are null or empty.
     *
     * @param PersonInterface $person
     * @param string          $email
     *
     * @throws Exception\PersonNotFoundException
     */
    private function validatePerson(PersonInterface $person, $email)
    {
        if (null !== $person->getFirstName()) {
            return;
        }

        if (null !== $person->getLastName()) {
            return;
        }

        if (null !== $person->getLocation()) {
            return;
        }

        if (null !== $person->getHeadline()) {
            return;
        }

        if (count($person->getImages()) > 0) {
            return;
        }

        if (count($person->getJobs()) > 0) {
            return;
        }

        if (count($person->getProfiles()) > 0) {
            return;
        }

        throw new PersonNotFoundException(sprintf(
            'The person with email "%s" was not found.',
            $email
        ));
    }

    /**
     * Gets a value from an array only if non-empty, otherwise returns null.
     *
     * @param array  $array
     * @param string $key
     *
     * @return string|null The non-empty value or null
     */
    private function getNonEmptyValue(array $array, $key)
    {
        $element = null;

        if (isset($array[$key])) {
            $element = $array[$key];

            if (empty($element)) {
                return null;
            }
        }

        return $element;
    }
}
