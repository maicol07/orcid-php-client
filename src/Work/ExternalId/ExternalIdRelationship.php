<?php

namespace Orcid\Work\ExternalId;

enum ExternalIdRelationship: string
{
    case SELF = 'self';
    case PART_OF = 'part-of';
    case VERSION_OF = 'version-of';
}
