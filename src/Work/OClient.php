<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;

use DOMException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use RuntimeException;
use Orcid\Oauth;

use function count;
use function is_array;

class OClient
{
    private Oauth $oauth;

    /**
     * OClient constructor.
     *
     * @throws RuntimeException
     */
    public function __construct(Oauth $oauth, bool $use_members_api = true)
    {
        $use_members_api ? $oauth->membersApi(true) : $oauth->membersApi(false);

        if (!$oauth->accessToken()) {
            throw new RuntimeException(
                'You must first set an access token or authenticate to exchange Work with ORCID'
            );
        }
        $this->oauth = $oauth;
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function readSummary(bool $json_response = true): OResponse
    {
        $contentType = $json_response ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->get($this->oauth->getApiEndpoint('works'), [
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->get($this->oauth->getApiEndpoint('works'));

        return new OResponse($response);
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function readSingle(int|string $put_code, bool $data_json_format = true): OResponse
    {
        $contentType = $data_json_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->get($this->oauth->getApiEndpoint('work') . '/' . $put_code, [
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->get($this->oauth->getApiEndpoint('work') . '/' . $put_code);
        return new OResponse($response);
    }

    /**
     * @param int[]|string[] $put_codes
     * @throws JsonException
     * @throws GuzzleException
     */
    public function readMultiple(array $put_codes, bool $data_json_format = true): OResponse
    {
        $contentType = $data_json_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        if (empty($put_codes)) {
            throw new RuntimeException('the work put-code array (worksIdArray) must not be empty');
        }
        if (count($put_codes) > 100) {
            throw new RuntimeException("You can't read more than 100 Work your work id array length is more than 100");
        }

        $put_codes_list = implode(',', $put_codes);
        $response = $this->oauth->client()
            ->get($this->oauth->getApiEndpoint('works') . '/' . $put_codes_list, [
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->get($this->oauth->getApiEndpoint('works') . '/' . $put_codes_list);
        return new OResponse($response);
    }

    /**
     * @param int|string|int[]|string[] $putCode
     * @throws JsonException
     */
    public function read(int|string|array $putCode): OResponse
    {
        return is_array($putCode) ? $this->readMultiple($putCode) : $this->readSingle($putCode);
    }

    /**
     * @throws GuzzleException|JsonException|DOMException
     */
    public function send(Work|Works $works): OResponse
    {
        $data = $works->getXMLData();
        if ($works instanceof Work) {
            return $this->postOne($data);
        }

        return $this->postMultiple($data);
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    protected function postOne(string $data, bool $json_data_format = false): OResponse
    {
        $contentType = $json_data_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->post($this->oauth->getApiEndpoint('work'), [
                'data' => $data,
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->post($this->oauth->getApiEndpoint('work'), $data);
        return new OResponse($response);
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    protected function postMultiple(string $data, bool $data_json_format = false): OResponse
    {
        $contentType = $data_json_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->post($this->oauth->getApiEndpoint('works'), [
                'form_params' => $data,
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->post($this->oauth->getApiEndpoint('works'), $data);
        return new OResponse($response);
    }

    /**
     * @throws DOMException|JsonException
     * @throws GuzzleException
     */
    public function update(Work $work, bool $data_json_format = false): OResponse
    {
        $putCode = $work->putCode();
        $data = $work->getXMLData();
        $contentType = $data_json_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->post($this->oauth->getApiEndpoint('work/' . $putCode), [
                'form_params' => $data,
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);

//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->post($this->oauth->getApiEndpoint('work/' . $putCode), $data);
        return new OResponse($response);
    }

    /**
     * @throws JsonException
     * @throws GuzzleException
     */
    public function delete(int|string $putCode, bool $data_json_format = true): OResponse
    {
        $contentType = $data_json_format ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response = $this->oauth->client()
            ->delete($this->oauth->getApiEndpoint('work/' . $putCode), [
                'headers' => [
                    'Accept' => $contentType,
                    'Content-Type' => $contentType,
                    'Authorization' => 'Bearer ' . $this->oauth->accessToken(),
                ],
            ]);
//        $response = $this->oauth->client()
//            ->accept($contentType)
//            ->withToken($this->oauth->accessToken())
//            ->delete($this->oauth->getApiEndpoint('work/' . $putCode));
        return new OResponse($response);
    }
}
