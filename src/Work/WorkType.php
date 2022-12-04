<?php

namespace Orcid\Work;

enum WorkType: string
{
    case ANNOTATION = 'annotation';
    case ARTISTIC_PERFORMANCE = 'artistic-performance';
    case BOOK_CHAPTER = 'book-chapter';
    case BOOK_REVIEW = 'book-review';
    case BOOK = 'book';
    case CONFERENCE_ABSTRACT = 'conference-abstract';
    case CONFERENCE_PAPER = 'conference-paper';
    case CONFERENCE_POSTER = 'conference-poster';
    case DATA_MANAGEMENT_PLAN = 'data-management-plan';
    case DATA_SET = 'data-set';
    case DICTIONARY_ENTRY = 'dictionary-entry';
    case DISCLOSURE = 'disclosure';
    case DISSERTATION_THESIS = 'dissertation';
    case EDITED_BOOK = 'edited-book';
    case ENCYCLOPEDIA_ENTRY = 'encyclopedia-entry';
    case INVENTION = 'invention';
    case JOURNAL_ARTICLE = 'journal-article';
    case JOURNAL_ISSUE = 'journal-issue';
    case LECTURE_SPEECH = 'lecture-speech';
    case LICENSE = 'license';
    case MAGAZINE_ARTICLE = 'magazine-article';
    case MANUAL = 'manual';
    case NEWSLETTER_ARTICLE = 'newsletter-article';
    case NEWSPAPER_ARTICLE = 'newspaper-article';
    case ONLINE_RESOURCE = 'online-resource';
    case OTHER = 'other';
    case PATENT = 'patent';
    case PHYSICAL_OBJECT = 'physical-object';
    case PREPRINT = 'preprint';
    case REGISTERED_COPYRIGHT = 'registered-copyright';
    case REVIEW = 'review';
    case REPORT = 'report';
    case RESEARCH_TECHNIQUE = 'research-technique';
    case RESEARCH_TOOL = 'research-tool';
    case SOFTWARE = 'software';
    case SPIN_OFF_COMPANY = 'spin-off-company';
    case STANDARDS_AND_POLICY = 'standards-and-policy';
    case SUPERVISED_STUDENT_PUBLICATION = 'supervised-student-publication';
    case TECHNICAL_STANDARD = 'technical-standard';
    case TEST = 'test';
    case TRANSLATION = 'translation';
    case TRADEMARK = 'trademark';
    case WEBSITE = 'website';
    case WORKING_PAPER = 'working-paper';
    case UNDEFINED = 'undefined';
}
