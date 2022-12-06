<?php

namespace Orcid\Work;

use PrinsFrank\Standards\Country\ISO3166_1_Alpha_2;
use PrinsFrank\Standards\Language\ISO639_1_Alpha_2;
use RuntimeException;

use function strlen;

/**
 * @method string|$this source(string $source = null)
 * @method string|$this lastModifiedDate(string $lastModifiedDate = null)
 * @method string|$this createdDate(string $createdDate = null)
 * @method string|$this visibility(string $visibility = null)
 * @method string|$this path(?string $path = null)
 * @method string|$this journalTitle(string $journalTitle = null)
 * @method string|$this shortDescription(string $shortDescription = null)
 * @method CitationType|$this citationType()
 * @method string|$this citation(string $citationValue = null, CitationType $citationType = CitationType::FORMATTED_UNSPECIFIED)
 * @method Contributor[]|$this contributors(Contributor[] $contributors = null)
 * @method Contributor[]|$this principalAuthors(Contributor[] $contributors = null)
 * @method ISO3166_1_Alpha_2|$this country(ISO3166_1_Alpha_2 $country = null)
 * @method ISO639_1_Alpha_2|$this languageCode(ISO639_1_Alpha_2 $language = null)
 * @method string|$this url(string $url = null)
 */
class Work extends OAwork
{
    use XMLWorkGeneration;
    protected int $last_modified_date;
    protected string $source;
    protected int $created_date;
    protected string $visibility;
    protected ?string $path = null;

    public static string $namespaceWork = 'http://www.orcid.org/ns/work';
    public static string $namespaceCommon = 'http://www.orcid.org/ns/common';
    public static string $namespaceBulk = 'http://www.orcid.org/ns/bulk';

    protected ?string $journal_title = null;
    protected ?string $short_description = null;
    protected ?string $citation = null;
    /** @var Contributor[] */
    protected ?array $contributors = null;
    /** @var Contributor[] */
    protected ?array $principal_authors = null;
    protected ?ISO639_1_Alpha_2 $language_code = null;
    protected ?CitationType $citation_type = null;
    protected ?ISO3166_1_Alpha_2 $country = null;
    protected string $url = '';

    protected function _property_setter(string $property, array $arguments): void
    {
        $value = $arguments[0] ?? null;
        switch ($property) {
            case 'short_description':
                if (empty($value) || strlen($value) > 5000) {
                    throw new RuntimeException('The short description must be a string and its length must not be than 5000 characters');
                }
                break;
            case 'citation':
                $this->citation_type = $arguments[1] ?? CitationType::FORMATTED_UNSPECIFIED;
        }
        parent::_property_setter($property, [$value]);
    }

    /**
     * An empty fullName string value will not be added
     * to be sure to add an author check on your side so that his full name is not empty.
     * if you added the author orcid ID and is from sandBox put false
     * for the last parameter $orcidIdOfProductionEnv (his default value is true)
     * this value will be use if only you add orcid ID
     * by default you can put empty string for $role and $sequence
     * but in this case we will add an author for empty role,
     * and we will not add sequence to the sent data
     * example : $work->('authorName','','0000-1111-2222-3333','',false)
     *
     * @throws RuntimeException
     */
    public function addContributor(
        string $full_name,
        ContributorRole $role,
        string $orcidID,
        ContributorSequence $sequence,
        bool $sandbox = true
    ): self {
        if (!empty($orcidID) && !preg_match('/(\d{4}-){3,}/', $orcidID)) {
            throw new RuntimeException('The author ' . $full_name . ' Orcid ' . $orcidID . ' is not valid');
        }

        if (!empty($full_name)) {
            $this->contributors[] = (new Contributor())
                ->creditName($full_name)
                ->role($role)
                ->orcid($orcidID)
                ->sandbox($sandbox)
                ->sequence($sequence);
        }
        return $this;
    }
}
