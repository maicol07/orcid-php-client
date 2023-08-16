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
    case CONCEPTUALIZATION = 'http://credit.niso.org/contributor-roles/conceptualization/';
    case DATA_CURATION = 'http://credit.niso.org/contributor-roles/data-curation/';
    case FORMAL_ANALYSIS = 'http://credit.niso.org/contributor-roles/formal-analysis/';
    case FUNDING_ACQUISITION = 'http://credit.niso.org/contributor-roles/funding-acquisition/';
    case INVESTIGATION = 'http://credit.niso.org/contributor-roles/investigation/';
    case METHODOLOGY = 'http://credit.niso.org/contributor-roles/methodology/';
    case PROJECT_ADMINISTRATION = 'http://credit.niso.org/contributor-roles/project-administration/';
    case RESOURCES = 'http://credit.niso.org/contributor-roles/resources/';
    case SOFTWARE = 'http://credit.niso.org/contributor-roles/software/';
    case SUPERVISION = 'http://credit.niso.org/contributor-roles/supervision/';
    case VALIDATION = 'http://credit.niso.org/contributor-roles/validation/';
    case VISUALIZATION = 'http://credit.niso.org/contributor-roles/visualization/';
    case WRITING_ORIGINAL_DRAFT = 'http://credit.niso.org/contributor-roles/writing-original-draft/';
    case WRITING_REVIEW_EDITING = 'http://credit.niso.org/contributor-roles/writing-review-editing/';
}
