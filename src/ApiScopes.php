<?php

namespace Orcid;

enum ApiScopes: string
{
    case AUTHENTICATE = '/authenticate';
    case READ_PUBLIC = '/read-public';
    case READ_LIMITED = '/read-limited';
    case ACTIVITIES_UPDATE = '/activities/update';
    case PERSON_UPDATE = '/person/update';
    case OPENID = '/openid';
    case WEBHOOK = '/webhook';
}
