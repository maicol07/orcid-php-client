<?php

namespace Orcid\Work\ExternalId;

enum ExternalIdType: string
{
    case AGRICOLA = 'agr';
    case ARCHIVAL_RESOURCE_KEY_IDENTIFIER = 'ark';
    case ARXIV = 'arxiv';
    case AMAZON_STANDARD_IDENTIFICATION_NUMBER = 'asin';
    case ASIN_TOP_LEVEL_DOMAIN = 'asin-tld';
    case AUTHENTICUS_ID = 'authenticusid';
    case BIBCODE = 'bibcode';
    case CHINESE_BIOLOGICAL_ABSTRACSTS = 'cba';
    case CULTUREGRAPH_NUMBER = 'cgn';
    case CIENCIA_IUL_IDENTIFIER = 'cienciaiul';
    case CITESEER = 'cit';
    case SCIENCE_AND_TECHNOLOGY_RESOURCE_IDENTIFICATION = 'cstr';
    case CITEEXPLORE_SUBMISSION = 'ctx';
    case GERMAN_NATIONAL_LIBRARY_IDENTIFIER = 'dnb';
    case DIGITAL_OBJECT_IDENTIFIER = 'doi';
    case SCOPUS_IDENTIFIER = 'eid';
    case ELECTRON_MICROSCOPY_DATA_BANK = 'emdb';
    case ELECTRON_MICROSCOPY_PUBLIC_IMAGE_ARCHIVE = 'empiar';
    case ETHOS_PERSISTENT_ID = 'ethos';
    case GRANT_NUMBER = 'grant_number';
    case HYPER_ARTICLES_EN_LIGNE = 'hal';
    case HANDLE = 'handle';
    case NHS_EVIDENCE = 'hir';
    case INTERNATIONAL_STANDARD_BOOK_NUMBER = 'isbn';
    case INTERNATIONAL_STANDARD_MUSIC_NUMBER = 'ismn';
    case INTERNATIONAL_STANDARD_SERIAL_NUMBER = 'issn';
    case JAHRBUCH_UBER_DIE_FORTSCHRITTE_DER_MATHEMATIK = 'jfm';
    case JSTOR_ABSTRACT = 'jstor';
    case K10PLUS = 'k10plus';
    case KOREAMED_UNIQUE_IDENTIFIER = 'kuid';
    case LIBRARY_OF_CONGRESS_CONTROL_NUMBER = 'lccn';
    case LENS_ID = 'lensid';
    case MATHEMATICAL_REVIEWS = 'mr';
    case ONLINE_COMPUTER_LIBRARY_CENTER = 'oclc';
    case OPEN_LIBRARY = 'ol';
    case OFFICE_OF_SCIENTIFIC_AND_TECHNICAL_INFORMATION = 'osti';
    case OTHER_IDENTIFIER_TYPE = 'other-id';
    case PATENT_NUMBER = 'pat';
    case PROTEIN_DATA_BANK_IDENTIFIER = 'pdb';
    case PUBMED_CENTRAL_ARTICLE_IDENTIFIER = 'pmc';
    case PUBMED_UNIQUE_IDENTIFIER = 'pmid';
    case EUROPE_PMC_PREPRINT_IDENTIFIER = 'ppr';
    case PROPOSAL_ID = 'proposal-id';
    case REQUEST_FOR_COMMENTS = 'rfc';
    case RESEARCH_RESOURCE_IDENTIFIER = 'rrid';
    case NON_STANDARD_ID_FROM_WORK_DATA_SOURCE = 'source-work-id';
    case SOCIAL_SCIENCE_RESEARCH_NETWORK = 'ssrn';
    case URI = 'uri';
    case URN = 'urn';
    case WEB_OF_SCIENCE_IDENTIFIER = 'wosuid';
    case ZENTRALBLATT_MATH = 'zbl';
}
