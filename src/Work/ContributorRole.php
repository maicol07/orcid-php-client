<?php

namespace Orcid\Work;

enum ContributorRole: string
{
    case AUTHOR = 'author';
    case ASSIGNEE = 'assignee';
    case EDITOR = 'editor';
    case CHAIR_OR_TRANSLATOR = 'chair-or-translator';
    case CO_INVESTIGATOR = 'co-investigator';
    case CO_INVENTOR = 'co-inventor';
    case GRADUATE_STUDENT = 'graduate-student';
    case OTHER_INVENTOR = 'other-inventor';
    case PRINCIPAL_INVESTIGATOR = 'principal-investigator';
    case POSTDOCTORAL_RESEARCHER = 'postdoctoral-researcher';
    case SUPPORT_STAFF = 'support-staff';
}
