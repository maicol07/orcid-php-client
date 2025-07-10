<?php

namespace Orcid\Work;

enum WorkType: string
{
    // Academic publications
    case BOOK = 'book';
    case BOOK_CHAPTER = 'book-chapter';
    case CONFERENCE_PAPER = 'conference-paper';
    case CONFERENCE_OUTPUT = 'conference-output';
    case CONFERENCE_PRESENTATION = 'conference-presentation';
    case CONFERENCE_POSTER = 'conference-poster';
    case CONFERENCE_PROCEEDINGS = 'conference-proceedings';
    case JOURNAL_ARTICLE = 'journal-article';
    case PREPRINT = 'preprint';
    case DISSERTATION_THESIS = 'dissertation-thesis';
    case WORKING_PAPER = 'working-paper';

    // Review and editing
    case ANNOTATION = 'annotation';
    case BOOK_REVIEW = 'book-review';
    case JOURNAL_ISSUE = 'journal-issue';
    case REVIEW = 'review';
    case TRANSCRIPTION = 'transcription';
    case TRANSLATION = 'translation';

    // Dissemination
    case BLOG_POST = 'blog-post';
    case DICTIONARY_ENTRY = 'dictionary-entry';
    case ENCYCLOPEDIA_ENTRY = 'encyclopedia-entry';
    case MAGAZINE_ARTICLE = 'magazine-article';
    case NEWSPAPER_ARTICLE = 'newspaper-article';
    case REPORT = 'report';
    case PUBLIC_SPEECH = 'public-speech';
    case WEBSITE = 'website';

    // Creative
    case ARTISTIC_PERFORMANCE = 'artistic-performance';
    case DESIGN = 'design';
    case IMAGE = 'image';
    case INTERACTIVE_RESOURCE = 'interactive-resource';
    case ONLINE_RESOURCE = 'online-resource';
    case MOVING_IMAGE_OR_VIDEO = 'moving-image';
    case MUSICAL_COMPOSITION = 'musical-composition';
    case SOUND = 'sound';

    // Data and process
    case CARTOGRAPHIC_MATERIAL = 'cartographic-material';
    case CLINICAL_STUDY = 'clinical-study';
    case DATA_SET = 'data-set';
    case DATA_MANAGEMENT_PLAN = 'data-management-plan';
    case PHYSICAL_OBJECT = 'physical-object';
    case RESEARCH_TECHNIQUE = 'research-technique';
    case RESEARCH_TOOL = 'research-tool';
    case SOFTWARE = 'software';

    // Legal and IP
    case INVENTION = 'invention';
    case LICENSE = 'license';
    case PATENT = 'patent';
    case REGISTERED_COPYRIGHT = 'registered-copyright';
    case STANDARDS_AND_POLICY = 'standards-and-policy';
    case TRADEMARK = 'trademark';

    // Teaching and supervision
    case LECTURE_SPEECH = 'lecture-speech';
    case TEACHING_MATERIAL = 'learning-object';
    case SUPERVISED_STUDENT_PUBLICATION = 'supervised-student-publication';

    // Legacy work types
    case CONFERENCE_ABSTRACT = 'conference-abstract';
    case DISCLOSURE = 'disclosure';
    case EDITED_BOOK = 'edited-book';
    case MANUAL = 'manual';
    case NEWSLETTER_ARTICLE = 'newsletter-article';
    case SPIN_OFF_COMPANY = 'spin-off-company';
    case TECHNICAL_STANDARD = 'technical-standard';
    case TEST = 'test';

    // Common types
    case OTHER = 'other';
    case UNDEFINED = 'undefined';
}
