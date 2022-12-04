<?php

namespace Orcid\Work;

use Orcid\DynamicClass;

/**
 * @method string|$this creditName(string $name = null)
 * @method string|$this email(string $email = null)
 * @method string|$this orcid(string $orcid = null)
 * @method ContributorSequence|$this sequence(ContributorSequence $sequence = null)
 * @method ContributorRole|$this role(ContributorRole $role = null)
 * @method bool|$this sandbox(bool $sandbox = null)
 */
class Contributor extends DynamicClass
{
    protected string $credit_name;
    protected string $email;
    protected string $orcid;
    protected ContributorSequence $sequence;
    protected ContributorRole $role;
    protected bool $sandbox;
}
