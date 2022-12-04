# Orcid PHP Client
PHP client to send and read workflows on orcid
> Based on [TKouchoanou/orcid-php-client](https://github.com/TKouchoanou/orcid-php-client)

This library was started to support the ORCID OAuth2 authentication workflow. It also supports basic profile access, but is a work in progress.
More features are to come as needed by the developer or requested/contributed by other interested parties.

## Usage
### OAuth2
#### 3-Legged Oauth Authorization
To go through the 3-legged oauth process, you must start by redirecting the user to the ORCID authorization page.

```php
use Orcid\Oauth;
use Orcid\ApiScopes;

// Set up the config for the ORCID API instance
$oauth = (new Oauth($clientId, $clientSecret))
    ->scopes(ApiScopes::AUTHENTICATE)
    ->state($state)
    ->redirectUri($redirectUri)
    ->showLogin(true);

// Create and follow the authorization URL
header("Location: " . $oauth->getAuthorizationUrl());
```

Most of the options described in the ORCID documentation (http://members.orcid.org/api/customize-oauth-login-screen)
concerning customizing the user authorization experience are encapsulated in the OAuth class.

Once the user authorizes your app, they will be redirected back to your redirect URI.
From there, you can exchange the authorization code for an access token.

```php
use Orcid\Oauth;
use Orcid\ApiScopes;

if (!isset($_GET['code'])) {
	// User didn't authorize our app
	throw new Exception('Authorization failed');
}

// Set up the config for the ORCID API instance
$oauth = (new Oauth($clientId, $clientSecret))
    ->redirectUri($redirectUri);

// Authenticate the user
$oauth->authenticate($_GET['code']);

// Check for successful authentication
if ($oauth->isAuthenticated()) {
	$orcid = new Profile($oauth);

	// Get ORCID iD
	$id = $orcid->id();
}
```

This example uses the ORCID public API. A members API is also available, but the OAuth process is essentially the same.
 
### Profile

As alluded to in the samples above, once successfully authenticated via OAuth, you can make subsequent requests to the other public/member APIs.
For example:

```php
use Orcid\Profile;
$orcid = new Profile($oauth);

// Get ORCID profile details
$id    = $orcid->id();
$email = $orcid->email();
$name  = $orcid->fullName();
```

The profile class currently only supports a limited amount helper methods for directly accessing elements from the profile data.
This will be expanded upon as needed.
The raw JSON data from the profile output is available by calling the `raw()` method.

Note that some fields (like email) may return null if the user has not made that field available.

### Environment and API types
ORCID supports two general API endpoints. The first is their public API,
and a second is for registered ORCID members (membership in this scenario does not simply mean that you have an ORCID account). 
The public API is used by default and currently supports all functionality provided by the library. 
You can, however, switch to the member API by calling:

```php
use Orcid\Oauth;
$oauth = (new Oauth())->membersApi(true);
```

If you explicitly want to use the public API, you can do so by calling:
```php
use Orcid\Oauth;
$oauth = (new Oauth())->membersApi(false);
```

ORCID also supports a sandbox environment designed for testing.
To use this environment, rather than the production environment (which is default), you can call the following command:
```php
use Orcid\Oauth;
$oauth = (new Oauth())->sandbox(true);
```

The counterpart to this function, though not explicitly necessary, is:
```php
use Orcid\Oauth;
$oauth = (new Oauth())->sandbox(false);
```

### Work
Work is a class that allows you to create a publication on your orcid account.
The data of a document to send to orcid must be added to a Work instance by the setters.
Work provides methods to create an XML document in the format accepted by Orcid.

```php
use Orcid\Work\Work;
use Orcid\Work\WorkType;
use PrinsFrank\Standards\Language\ISO639_1_Alpha_2;
use Orcid\Work\CitationType;
use PrinsFrank\Standards\Country\ISO3166_1_Alpha_2;
use Orcid\Work\ContributorRole;
use Orcid\Work\ContributorSequence;
// creation of an Orcid work
$work = (new Work())
    ->title("Les stalagmites du réseau du trou Noir")
    ->translatedTitle('The stalagmites of the Black hole network')
    ->translatedTitleLanguageCode(ISO639_1_Alpha_2::English) // These three methods about the title can be shortened to the first one (3 params)
    ->type(WorkType::WORKING_PAPER)
    ->url("the work url")
    ->journalTitle("naturephysic")
    ->citation("The work citation....")//if you don't set citationType formatted-unspecified will be set
    ->citationType(CitationType::FORMATTED_UNSPECIFIED)
    ->shortDescription("the work description...") // the description must be less than 500 characters
    ->publicationDate(\Carbon\Carbon::createFromDate(1998, 09, 20)) // the first parameter year is required if you want to set date
    ->languageCode(ISO639_1_Alpha_2::French)
    ->country(ISO3166_1_Alpha_2::United_States_of_America_the)
    //add Authors with Author FullName and role, by default the role 'author' will be chosen your can also add the orcidID and the sequence of author
    ->addContributor("Benjamin Lans", ContributorRole::AUTHOR,"1111-OOOO-2543-3333", ContributorSequence::FIRST)
    ->addContributor("Richard Maire", ContributorRole::EDITOR)
    ->addContributor("Richard Ortega", ContributorRole::CO_INVENTOR)
    ->addContributor("Guillaume Devès", ContributorRole::CO_INVESTIGATOR,"OOOO-1111-2222-3333", ContributorSequence::ADDITIONAL)
    //add subtitle
    ->subtitle("subtitle three")
    // add External Ident the type , the value, the url, the relationship by default url will be empty and relationship will be self . idtype and idValue   are required
    ->addExternalId("doi", "10.1038/nphys1170", "https://www.nature.com/articles/nphys1170")
    ->addExternalId("uri", "00199711");
```
The minimum configuration for sending an Orcid Work is to define the title, the type of document and add at least an external identifier.
```php
use Orcid\Work\Work;
use Orcid\Work\WorkType;
 // minimum configuration to create an Orcid work
$work = (new Work())
    ->title("Les stalagmites du réseau du trou Noir")
    ->type(WorkType::WORKING_PAPER)
    ->addExternalId("doi", "10.1038/nphys1170");
```

If you want to edit a work, put-code is required.
```php
$putCode = 14563; 
$work->putcode($putCode); 
```
### Works
Works is a class that inherits from arrayIterator. It is a list of orcid works to which we can add instances of type Work with the append method

```php
use Orcid\Work\Works;
$works = new Works([$work1, $work2, $work3]);
// Or via the append method:
$works->append($firstwork);
$works->append($secondwork);
$works->append($thirdwork);
```

We can iterate with foreach on works:

```php
use Orcid\Work\Work;

foreach ($works as $work){
    assert($work instanceof Work);
    $work->addContributor("Authorfullname", \Orcid\Work\ContributorRole::AUTHOR, "Author orcid ID", \Orcid\Work\ContributorSequence::FIRST); 
}
```

### OClient
An Orcid client makes it possible to communicate with the orcid count whose authentication elements are contained in the Oauth object,
which is passed to the Oclient constructor.
 
```php
use Orcid\Work\OClient;
// Check for successful authentication

if ($oauth->isAuthenticated())
{
    // creation of an orcid client
	$orcidClient = new OClient($oauth);
}
```

The different methods of Oclient are:

Send: allows you to send one or more publications.
It takes as parameter an array of instance of the work class,
an instance of works to send several publications or an instance of the work class to send a single publication.

```php
use Orcid\Work\OClient;
// send one or several work(s)
/** @var Work|Works|Work[] $works  */
/** @var OClient $orcidClient  */
$orcidClient->send($works); 
```

Update: This method allows you to modify a Work already sent to Orcid. You can only modify a work already present in an orcid account with a putCode to recover.To modify don't forget to set putCode
```php
use Orcid\Work\OClient;
// update a Work

/** @var OClient $orcidClient  */
/** @var OClient $orcidClient  */

$orcidClient->update($work); 
```

Delete: allows you to delete a job. It takes as parameter the putCode of work on orcid
```php
use Orcid\Work\OClient;
// delete a Work
$putcode=14563;
/** @var OClient $orcidClient  */
$orcidClient->delete($putcode); 
```

ReadSummary: Allows you to read all the works present Orcid registration of the account holder represented by `$oauth`. 
```php
use Orcid\Work\OClient;
/** @var OClient $orcidClient  */
// read Summary
$orcidClient->readSummary()
```

Read : Allows you to read one or more records by taking its parameter a putCode of type int or string or an array of putCode.
The putCode must be a numeric value, it is returned by orcid
```php
use Orcid\Work\OClient;
// read work(s)
/** @var int|string|int[]|string[] $putCode */
/** @var OClient $orcidClient  */
$orcidClient->read($putCode);
```

### OResponse
It is a response object returned by Oclient methods. It contains the information of the response returned by Orcid. Requests are made with curl
```php
use Orcid\Work\OClient;

/** @var OClient $orcidClient  */
$OResponse= $orcidClient->readSummary();
$code=$OResponse->getErrorCode();
$header=$OResponse->response->getHeaders();
$body=$OResponse->response->getBody()->getContents();
```

If the request errors, Orcid returns data, which can be retrieved by these methods,
which will return null or an empty string if there has been no error.
```php
/** @var \Orcid\Work\OResponse $OResponse  */
if ($OResponse->hasError()) {
    $errorCode = $OResponse->getErrorCode();
    $userMessage = $OResponse->user_message;
    $developerMessage = $OResponse->developer_message;
}
```
About reading all the work records in an orcid account with the ReadSummary method Oresponse has a method,
which returns the list of Orcid records read.
This method returns null if Oresponse is not the response to a call to the ReadSummary method.
```php
use Orcid\Work\Works;
/** @var \Orcid\Work\OResponse $OResponse  */

if ($OResponse->hasSuccess()) {
    /** @var Records $recordWorkList */
    $recordWorkList = $OResponse->getWorkRecordList(); 
}
```
This method returns an instance of Records, which is a list of Record instances
### Records and Record
It is an instance whose set of properties represents an orcid work from the user's orcid account. It has some properties in common with the Work instance (the class used to create a work to send to Orcid) and specific properties coming from orcid

```php
use Orcid\Work\Works;
use Orcid\Work\Work;
/** @var Works $works */

// Returns date of last modification of ORCID registrations
$grouplastModifiedDate = $works->lastModifiedDate(); 

// returns a complex associative array coming directly from Orcid and containing the information on the work read.
$group = $works->getOrcidWorks(); 

foreach ($works as $work){
    assert($work instanceof Work);
    $putCode= $work->putCode();
    $workSource = $work->source();
    $workPath = $work->path();
    $lastModifiedDate = $work->lastModifiedDate(); // returns date of last modification of this record work
    $title = $work->title();

    // returns an external identifier array of type ExternalId
    $externalIds = $work->externals();
}
 ```
 
### ExternalId
Represents an external identifier and contains the four properties $ idType, $ idValue, $ idUrl, $ idRelationship

```php
use Orcid\Work\ExternalId;
use Orcid\Work\Work;

/** @var Work $record */

$externalIds= $record->externals();

foreach ($externalIds as $externalId){
    assert($externalId instanceof ExternalId);
    $idType = $externalId->type();
    $idValue = $externalId->value(); 
    $idUrl = $externalId->url(); 
    $idRelationship = $externalId->relationship();
}
```
