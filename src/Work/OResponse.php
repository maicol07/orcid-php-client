<?php
namespace Orcid\Work;

//use Illuminate\Http\Client\Response;
use JsonException;
use Psr\Http\Message\ResponseInterface;

use function count;
use function in_array;

class OResponse
{
    public const SUCCESS_CODE = [200, 201, 202, 203, 204];

    public readonly ?string $developer_message;
    public readonly ?string $user_message;
    public readonly ?string $error_code;
    public readonly ?string $more_info;
    private ?Works $work_records = null;
    private string $body;

    /**
     * @throws JsonException
     */
    public function __construct(
        public readonly ResponseInterface $response
//        public readonly Response $response
    ) {
//        $body = $this->response->body();
        $this->body = $this->response->getBody()->getContents();
        if (self::isXmlString($this->body)) {
            $xml = simplexml_load_string($this->body);
            $this->body = json_encode($xml, JSON_THROW_ON_ERROR);
        }
        $json = json_decode($this->body, false, 512, JSON_THROW_ON_ERROR);

        $this->developer_message = $json->error ?? $json->{'developer-message'} ?? null;
        $this->user_message = $json->{'user-message'} ?? null;
        $this->error_code = $json->{'error-code'} ?? null;
        $this->more_info = $json->{'more-info'} ?? null;
    }

    protected function setWorkRecords(): self
    {
//        $records = $this->response->json();
        $records = json_decode($this->body, true, 512, JSON_THROW_ON_ERROR);

        if (isset($records['last-modified-date'], $records['group'], $records['path'])) {
            $records = array_column(array_column($records['group'], 'work-summary'), 0);
        } elseif (isset($records['bulk'])) { // Bulk
//            dd($this->response, $records);
            $records = array_column($records['bulk'], 'work');
        }

        if (count($records) >= 100) {
            $this->work_records = new Works(array_splice($records, 0, 100));
        }
        else{
            $this->work_records = new Works($records);
        }
        return $this;
    }

    /**
     * @return Works<Work>
     * @throws JsonException
     */
    public function getWorkRecords(): Works
    {
        if (empty($this->work_records)) {
            $this->setWorkRecords();
        }
        return $this->work_records;
    }

    public function getErrorCode(): int|string|null
    {
        if (empty($this->error_code) && !$this->hasSuccess() && !$this->hasConflict()) {
//            return $this->response->status();
            return $this->response->getStatusCode();
        }
        return $this->error_code;
    }

    public function hasError(): bool
    {
        return !empty($this->getErrorCode());
    }

    public function hasSuccess(): bool
    {
        return in_array($this->response->getStatusCode(), self::SUCCESS_CODE, true);
    }

    public function hasConflict(): bool
    {
        return $this->response->getStatusCode() === 409;
    }

    private static function isXmlString(string $xmlString): false|int
    {
        $regex = '/<\?xml .+\?>/';
        return preg_match($regex, $xmlString);
    }
}
