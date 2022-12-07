<?php

namespace Orcid\Work;

enum WorkVisibility: string
{
    case PUBLIC = 'public';
    case TRUSTED_PARTIES = 'trusted-parties';
    case PRIVATE = 'private';
}
