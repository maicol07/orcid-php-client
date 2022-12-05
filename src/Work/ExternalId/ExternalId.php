<?php
namespace Orcid\Work\ExternalId;

use RuntimeException;

class ExternalId
{

    /**
     * ExternalId constructor.
     *
     * @throws RuntimeException
     */
    public function __construct(
        public ExternalIdType $type,
        public string $value,
        public string $url,
        public ExternalIdRelationship $relationship = ExternalIdRelationship::SELF)
    {
        if (empty($this->value)) {
            throw new RuntimeException('ExternalId value cannot be empty');
        }
    }
}
