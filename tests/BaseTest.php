<?php /** @noinspection LaravelFunctionsInspection */

use Orcid\Oauth;
use Orcid\Profile;
use Orcid\Work\OClient;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    public function OAuth(): Oauth {
        return (new Oauth(env('ORCID_CLIENT_ID'), env('ORCID_CLIENT_SECRET')))
            ->accessToken(env('ORCID_ACCESS_TOKEN'))
            ->orcid(env('ORCID_ID'))
            ->sandbox(env('ORCID_SANDBOX', true))
            ->membersApi(env('ORCID_MEMBERS_API', true));
    }

    public function OClient(): OClient {
        return new OClient($this->OAuth());
    }

    public function Profile(): Profile {
        return new Profile($this->OAuth());
    }
}
