<?php
namespace Orcid\Work;

use Illuminate\Http\Client\Response;
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

    /**
     * @throws JsonException
     */
    public function __construct(
        public readonly Response $response
    ) {
        $body = $this->response->body();
        if (self::isXmlString($body)) {
            $xml = simplexml_load_string($body);
            $body = json_encode($xml, JSON_THROW_ON_ERROR);
            $json = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } else {
            $json = $this->response->object();
        }
        $this->developer_message = $json->error ?? $json->{'developer-message'} ?? null;
        $this->user_message = $json->{'user-message'} ?? null;
        $this->error_code = $json->{'error-code'} ?? null;
        $this->more_info = $json->{'more-info'} ?? null;
    }

    protected function setWorkRecords(): self
    {
        $records = $this->response->json();
        if (isset($records['last-modified-date'], $records['group'], $records['path'])) {
            $records = array_column(array_column($records['group'], 'work-summary'), 0);
        } else { // Bulk
            $records = array_column($records['bulk'], 'work');
        }
        $this->work_records = new Works($records);
        return $this;
    }

    /**
     * @return Works<Work>
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
            return $this->response->status();
        }
        return $this->error_code;
    }

    public function hasError(): bool
    {
        return !empty($this->getErrorCode());
    }

    public function hasSuccess(): bool
    {
        return in_array($this->response->status(), self::SUCCESS_CODE);
    }

    public function hasConflict(): bool
    {
        return $this->response->status() === 409;
    }

    private static function isXmlString(string $xmlString): false|int
    {
        $regex = '/<\?xml .+\?>/';
        return preg_match($regex, $xmlString);
    }
}
