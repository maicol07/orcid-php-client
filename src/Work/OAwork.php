<?php

namespace Orcid\Work;

use Carbon\Carbon;
use Orcid\DynamicClass;
use Orcid\Work\ExternalId\ExternalId;
use Orcid\Work\ExternalId\ExternalIdRelationship;
use Orcid\Work\ExternalId\ExternalIdType;

use PrinsFrank\Standards\Language\ISO639_1_Alpha_2;
use RuntimeException;

/**
 * @method ExternalId[]|$this externals(ExternalIdType $type = null, string $value = null, string $url = null, string $relationship = null)
 * @method int|string|$this putCode(int|string $putCode = null)
 * @method string|$this title(string $title = null, string $translated_title = null, ISO639_1_Alpha_2 $translated_language_code = null)
 * @method string|$this translatedTitle(string $translated_title = null, ISO639_1_Alpha_2 $translated_language_code = null)
 * @method ISO639_1_Alpha_2|$this translatedTitleLanguageCode(ISO639_1_Alpha_2 $translated_title_language_code = null)
 * @method string|$this subtitle(string $subtitle = null)
 * @method WorkType|$this type(WorkType $type = null)
 * @method Carbon|$this publicationDate(Carbon $publicationDate = null)
 */
abstract class OAwork extends DynamicClass
{
    use ODataValidator;

    public const SPECIAL_LANGAGE_CODES = [
        'zh_cn' => 'zh_CN',
        'ZH_CN' => 'zh_CN',
        'zh_tw' => 'zh_TW',
        'ZH_TW' => 'zh_TW'
    ];

    protected string $put_code;
    protected string $title;
    protected ?string $translated_title;
    protected ?ISO639_1_Alpha_2 $translated_title_language_code;
    protected string $subtitle;
    protected Carbon $publication_date;
    /** @var ExternalId[] */
    protected array $externals = [];
    protected WorkType $type;

    protected function _property_setter(string $property, array $arguments): void
    {
        $value = $arguments[0] ?? null;
        switch ($property) {
            case 'putcode':
                if (empty($value) || !is_numeric($value)) {
                    throw new RuntimeException(
                        'The putcode of work must be numeric and not empty, you try to set a value which is not numeric or is empty'
                    );
                }
                break;
            case 'title':
                if (empty($value)) {
                    throw new RuntimeException(
                        'The title of work must not be empty'
                    );
                }
                $this->translated_title = $arguments[1] ?? null;
                $this->translated_title_language_code = $arguments[2] ?? null;
                break;
            case 'translated_title':
                if (empty($value)) {
                    throw new RuntimeException(
                        'The translated title of work must not be empty'
                    );
                }
                break;
        }
        parent::_property_setter($property, [$value]);
    }

    /**
     * possible to add several external ID of the same type
     * But you are responsible for what you send.
     *
     * @throws RuntimeException
     */
    public function addExternalId(ExternalIdType $type, string $externalIdValue, string $externalIdUrl, ExternalIdRelationship $externalIdRelationship = null): self {
        $this->externals[] = new ExternalId($type, $externalIdValue, $externalIdUrl, $externalIdRelationship);
        return $this;
    }
}
