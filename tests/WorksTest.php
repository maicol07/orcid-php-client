<?php

use GuzzleHttp\Exception\ClientException;
use Orcid\Work\Work;

class WorksTest extends BaseTest
{
    public function testReadSummary(): void
    {
        $works = $this->OClient()->readSummary()->getWorkRecords();
        foreach ($works as $work) {
            $this->assertInstanceOf(Work::class, $work);
        }
        $this->assertNotEmpty($works);
    }

    public function testReadMultiple(): void
    {
        $put_codes = $this->OClient()->readSummary()->getWorkRecords()[0]->putCode();
        $works = $this->OClient()->readMultiple([$put_codes])->getWorkRecords();
        foreach ($works as $work) {
            $this->assertInstanceOf(Work::class, $work);
        }
        $this->assertNotEmpty($works);
    }

    public function testSendOneRaw() {
        $xml = env('ORCID_TEST_WORK_XML');
        try {
            $this->OClient()->sendRaw($xml);
            $this->assertTrue(true);
        } catch (ClientException $e) {
            dump($e->getRequest(), $e->getRequest()->getBody()->getContents());
            dump($e->getResponse(), $e->getResponse()->getBody()->getContents());
            $this->fail();
        }
    }
}
