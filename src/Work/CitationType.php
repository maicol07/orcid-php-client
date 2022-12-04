<?php

namespace Orcid\Work;

enum CitationType: string
{
    case FORMATTED_UNSPECIFIED = 'formatted-unspecified';
    case BIBTEX = 'bibtex';
    case RIS = 'ris';
    case FORMATTED_APA = 'formatted-apa';
    case FORMATTED_HERVARD = 'formatted-harvard';
    case FORMATTED_IEEE = 'formatted-ieee';
    case FORMATTED_MLA = 'formatted-mla';
    case FORMATTED_VANCOUVER = 'formatted-vancouver';
    case FORMATTED_CHICAGO = 'formatted-chicago';
}
