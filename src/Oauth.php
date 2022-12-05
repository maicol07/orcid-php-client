<?php /** @noinspection PhpUnusedprotectedFieldInspection */

namespace Orcid;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

//use Illuminate\Http\Client\Factory;
//use Illuminate\Http\Client\PendingRequest;
use JsonException;
use RuntimeException;

use function strlen;

const ORCID_API_HOSTNAME = 'orcid.org/v3.0/';
const ORCID_SANDBOX_HOSTNAME = 'sandbox.orcid.org/v3.0/';

/**
 * Orcid api oauth class.
 *
 * @method string|self clientId(string $value = null) Sets the client ID.
 * @method string|self clientSecret(string $value = null) Sets or get the client secret.
 * @method string|self orcid(string $value = null) Sets or get the orcid ID. Use this to pre-fill the sign-in page shown to the user.
 * @method string|self name(string $value = null) Sets or get the username.
 * @method string|self email(string $value = null) Sets or get the user email. Use this to pre-fill the email address on the login/registration form that ORCID will present when the user is taken to their site for authentication/registration.
 * @method string|self familyNames(string $value = null) Sets or get the user family names. Use this to pre-fill the family names on the login/registration form that ORCID will present when the user is taken to their site for authentication/registration.
 * @method string|self givenNames(string $value = null) Sets or get the user given names. Use this to pre-fill the given names on the login/registration form that ORCID will present when the user is taken to their site for authentication/registration.
 * @method bool|self membersApi(bool $value = null) Check if the api is members api or not or set it to use members api or public api.
 * @method bool|self sandbox(bool $value = null) Check if the environment is sandbox or not or set it to use sandbox or production environment.
 * @method ApiScopes[]|self scopes(ApiScopes ...$value = null) Sets or get the oauth scope. This is the scope of the permissions you'll be requesting from the user. See ORCID documentation for options and more details. Although the doc is somewhat unclear, I don't think you can request more than '/authorize' if you intend to use the public api.
 * @method string|self state(string $value = null) Sets or get the oauth state. This isn't necessarily required, but serves as a CSRF check, as well as an easy way to retain information between the initial login redirect and the user coming back to your site. In theory, you should set this and then verify it after it comes back.
 * @method string|self redirectUri(string $value = null) Sets or get the oauth redirect uri. This is where the user will come back to after their interaction with the ORCID login/registration page.
 * @method string|self accessToken(string $value = null) Sets or get the oauth access token. This is the token you'll use to make requests to the ORCID api.
 * @method string|self refreshToken(string $value = null) Sets or get the oauth refresh token.
 * @method string|self expiresIn(string $value = null) Sets or get the oauth expires in.
 * @method bool|self showLogin(bool $value = null) Sets the show_login flag to tell ORCID to show the login page, rather than the registration page when the user initially arrives.
 * @method array|self authenticateData(array $value = null) Sets the authenticate_data flag to tell ORCID to show the login page, rather than the registration page when the user initially arrives.
 */
class Oauth extends DynamicClass
{
    protected bool $members_api = false;
    protected bool $sandbox = false;

    /**
     * The oauth request scopes
     * @var ApiScopes[]
     */
    protected array $scopes = [];

    /** The oauth request state */
    protected string $state;

    /** The oauth redirect URI */
    protected string $redirect_uri;

    /** The login/registration page email address */
    protected string $email;

    /** The login/registration page orcid */
    protected string $orcid;

    /** The login/registration page family name */
    protected string $family_names;

    /** The login/registration page given name */
    protected string $given_names;

    /** Whether to show the login page as opposed to the registration page. */
    protected bool $show_login = false;

    /** The oauth access token */
    protected string $access_token;
    protected array $authenticate_data;
    protected string $refresh_token;
    protected int $expires_in = 0;
    protected string $name;

    public function __construct(
        protected string $client_id,
        protected string $client_secret,
    ) {
        /** @noinspection UnusedFunctionResultInspection */
        $this->clientId($client_id)
            ->clientSecret($client_secret);
    }

    protected function _property_setter(string $property, array $arguments): void
    {
        $value = $arguments[0];
        $value = match ($property) {
            'client_id', 'client_secret', 'orcid' => trim($value),
            'scopes' => [...$arguments],
            default => $value
        };
        parent::_property_setter($property, [$value]);
    }

    /**
     * Gets the authorization URL based on the instance parameters
     *
     * @throws RuntimeException
     */
    public function getAuthorizationUrl(): string
    {
        // Check for required items
        if (!$this->clientId()) {
            throw new RuntimeException('Client ID is required');
        }
        if (!$this->scopes()) {
            throw new RuntimeException('Scope is required');
        }
        if (!$this->redirectUri()) {
            throw new RuntimeException('Redirect URI is required');
        }


        // Start building url (endpoint is the same for public and member APIs)
        return $this->getApiEndpoint('oauth/authorize') . http_build_query([
            'client_id' => $this->clientId(),
            'response_type' => 'code',
            'scope' => implode(' ', $this->scopes()),
            'redirect_uri' => $this->redirectUri(),
            'state' => $this->state(),
            'show_login' => $this->showLogin(),
            'orcid' => $this->orcid(),
            'email' => $this->email(),
            'family_names' => $this->familyNames(),
            'given_names' => $this->givenNames(),
        ]);
    }

    /**
     * Takes the given code and requests an auth token
     *
     * @param string $code the oauth code needed to request the access token
     * @return  $this
     * @throws  RuntimeException
     **/
    public function authenticate(string $code): self
    {
        // Validate code
        if (!$code || strlen($code) !== 6) {
            throw new RuntimeException('Invalid authorization code');
        }

        // Check for required items
        if (!$this->clientId()) {
            throw new RuntimeException('Client ID is required');
        }
        if (!$this->clientSecret()) {
            throw new RuntimeException('Client secret is required');
        }
        /**
         * if (!$this->redirectUri) {
         * throw new Exception('Redirect URI is required');
         * }
         * */

        $fields = [
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'code' => $code,
            //   'redirect_uri'  => urlencode($this->redirectUri),
            'grant_type' => 'authorization_code'
        ];

        $response = $this->client()
            ->post($this->getApiEndpoint('oauth/token'), [
                'form_params' => $fields,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
//            ->acceptJson()
//            ->asForm()
//            ->post($this->getApiEndpoint('oauth/token'), $fields);

//        $data = $response->object();
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($data->access_token)) {
            $this->accessToken($data->access_token)
                ->orcid($data->orcid)
                ->refreshToken($data->refresh_token)
                ->name($data->name)
                ->expiresIn($data->expires_in)
                ->authenticateData((array) $data);
        } else {
            // Seems like the response format changes on occasion… not sure what's going on there?
            $error = $data->error_description ?? $data->{'error-desc'}->value;

            throw new RuntimeException($error);
        }

        return $this;
    }

    /**
     * Checks for access token to indicate authentication
     **/
    public function isAuthenticated(): bool
    {
        return !empty($this->accessToken());
    }

    /**
     * Grabs the user's profile
     *
     * You'll probably call this method after completing the proper oauth exchange.
     * But, in theory, you could call this without oauth and pass in a ORCID iD,
     * assuming you use the public API endpoint.
     *
     * @param string|null $orcid the orcid to look up, if not already set as class property.
     */
    public function getProfile(string $orcid = null): object {
        $client = $this->client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        if ($this->membersApi()) {
            // If using the members api, we have to have an access token set.
            if (!$this->accessToken()) {
                throw new RuntimeException('You must first set an access token or authenticate');
            }

//            $client = $client->withToken($this->accessToken());
        }

//        $response = $client
//            ->accept('application/vnd.orcid+json')
//            ->get($this->getApiEndpoint('record', $orcid));
        $response = $client->get($this->getApiEndpoint('record', $orcid), [
            'headers' => $headers
        ]);

//        return $response->object();
        return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Creates the qualified api endpoint for retrieving the desired data
     *
     * @param string|null $endpoint the shortname of the endpoint
     * @param string|null $orcid the orcid to look up, if not already specified
     */
    public function getApiEndpoint(string $endpoint = null, string $orcid = null): string
    {
        return ($this->orcid() ?? $orcid) . "/$endpoint";
    }

    public function client(): Client { // PendingRequest {
//        return (new Factory())
//            ->baseUrl('https://' . ($this->membersApi() ? 'api' : 'pub') . '.' . ($this->sandbox ? ORCID_SANDBOX_HOSTNAME : ORCID_API_HOSTNAME));
        return new Client([
            'base_uri' => 'https://' . ($this->membersApi() ? 'api' : 'pub') . '.' . ($this->sandbox() ? ORCID_SANDBOX_HOSTNAME : ORCID_API_HOSTNAME)
        ]);
    }
}
