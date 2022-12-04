<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr | theophane_malo@yahoo.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;

use function is_array;

/**
 * ORCID profile API class
 **/
class Profile
{
    private Oauth $oauth;

    /**
     * The raw orcid profile
     **/
    private object $raw;

    /**
     * Constructs object instance
     *
     * @param Oauth|null $oauth the oauth object used for making calls to orcid
     */
    public function __construct(Oauth $oauth = null)
    {
        $this->oauth = $oauth;
    }

    /**
     * Grabs the ORCID iD
     */
    public function id(): string
    {
        return $this->oauth->orcid();
    }

    /**
     * Grabs the orcid profile (oauth client must have requested this level or access)
     */
    public function raw(): object
    {
        if (!isset($this->raw)) {
            $this->raw = $this->oauth->getProfile($this->id());
        }

        return $this->raw;
    }

    /**
     * Grabs the ORCID person
     **/
    public function person(): object
    {
        $this->raw();

        return $this->raw->person;
    }

    /**
     * Grabs the users email if it is set and available
     *
     * @throws GuzzleException|JsonException
     **/
    public function email(): ?string
    {
        $this->raw();

        $email = null;
        $person = $this->person();

        if (isset($person->emails->email[0]) && is_array($person->emails->email)) {
            $email = $person->emails->email[0]->value;
        }

        return $email;
    }

    /**
     * Grabs the raw name elements to create fullname.
     *
     * @throws GuzzleException|JsonException
     **/
    public function fullName(): string
    {
        $this->raw();
        $details = $this->person()->name;

        // "given-names" is a required field on ORCID profiles.
        // "family-name", however, may or may not be available.
        // https://members.orcid.org/api/tutorial/reading-xml#names
        return $details->{'given-names'}->value . ($details->{'family-name'} ? ' ' . $details->{'family-name'}->value : '');
    }
}
