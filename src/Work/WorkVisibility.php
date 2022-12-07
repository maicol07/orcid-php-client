<?php

namespace Orcid\Work;

enum WorkVisibility: string
{
    case PUBLIC = 'public';
    case LIMITED_TO_TRUSTED_PARTIES = 'limited';
    case PRIVATE = 'private';
}
