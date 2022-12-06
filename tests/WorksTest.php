<?php

use Orcid\Work\Work;
use Orcid\Work\Works;

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
}
