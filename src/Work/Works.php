<?php

namespace Orcid\Work;

use ArrayAccess;
use ArrayIterator;
use Carbon\Carbon;
use DOMException;
use Iterator;
use IteratorAggregate;
use Orcid\Work\ExternalId\ExternalIdRelationship;
use Orcid\Work\ExternalId\ExternalIdType;
use PrinsFrank\Standards\Language\ISO639_1_Alpha_2;
use RuntimeException;

use function assert;
use function count;
use function is_array;

/**
 * @template <T = Work>
 */
class Works implements IteratorAggregate, ArrayAccess
{
    public array $works;

    /** @noinspection UnusedFunctionResultInspection */
    public function __construct(array $works = [])
    {
        foreach ($works as $work) {
            $new_work = (new Work())
                ->putCode($work['put-code'])
                ->title($work['title']['title']['value'])
                ->source($work['source']['source-name']['value'])
                ->lastModifiedDate($work['last-modified-date']['value'])
                ->createdDate($work['created-date']['value'])
                ->type(WorkType::from($work['type']))
                ->path($work['path'])
                ->visibility($work['visibility'])
                ->publicationDate(
                    Carbon::create(
                        $work['publication-date']['year']['value'] ?? null,
                        $work['publication-date']['month']['value'] ?? null,
                        $work['publication-date']['day']['value'] ?? null
                    )
                );

            $translatedTitle = $work['title']['translated-title']['value'] ?? null;
            $translatedTitleLanguageCode = $work['title']['translated-title']['language-code'] ?? null;
            $subTitle = $work['title']['subtitle']['value'] ?? null;

            $externalIdArray = $work['external-ids']['external-id'] ?? [];

            if (!empty($translatedTitle)) {
                $new_work->translatedTitle($translatedTitle);
            }
            if (!empty($translatedTitleLanguageCode)) {
                $new_work->translatedTitleLanguageCode(ISO639_1_Alpha_2::from($translatedTitleLanguageCode));
            }
            if (!empty($subTitle)) {
                $new_work->subtitle($subTitle);
            }

            $citation = $work['citation'] ?? null;
            if (is_array($citation)) {
                $new_work->citation($citation['citation-value'], CitationType::tryFrom($citation['citation-type']) ?? CitationType::FORMATTED_UNSPECIFIED);
            }

            foreach ($externalIdArray as $externalId) {
                $relationType = $externalId['external-id-relationship'] ?? '';
                $url = $externalId['external-id-url']['value'] ?? '';
                $type = $externalId['external-id-type'];
                $value = $externalId['external-id-value'];
                $new_work->addExternalId(
                    ExternalIdType::from($type),
                    $value,
                    $url,
                    ExternalIdRelationship::from($relationType)
                );
            }

            foreach ($work['contributors']['contributor'] ?? [] as $index => $contributor) {
                $new_work->addContributor(
                    $contributor['credit-name']['value'],
                    ContributorRole::from($contributor['contributor-attributes']['contributor-role'] ?? ContributorRole::AUTHOR->value),
                    $contributor['contributor-orcid']['path'] ?? '',
                    ContributorSequence::from($contributor['contributor-attributes']['contributor-sequence'] ?? ($index === 0 ? ContributorSequence::FIRST : ContributorSequence::ADDITIONAL)->value),
                    str_contains($contributor['contributor-orcid']['path'] ?? '', 'sandbox')
                );
            }

            $this->append($new_work);
        }
    }

    public function append(mixed $value): void
    {
        if ($value instanceof Work) {
            $this->works[] = $value;
        } else {
            throw new RuntimeException('The value you want to append must be instance of Record and not null.');
        }
    }

    public function isEmpty(): bool
    {
        return count($this->works) === 0;
    }

    /**
     * @throws DOMException
     */
    public function getXMLData(): false|string
    {
        $dom = Work::getNewOrcidCommonDomDocument();
        $bulk = $dom->appendChild($dom->createElementNS(Work::$namespaceBulk, 'bulk:bulk'));
        $dom->createAttributeNS(Work::$namespaceWork, 'work:work');
        $dom->createAttributeNS(Work::$namespaceCommon, 'common:common');
        $bulk->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            Work::$namespaceBulk . ' ../ bulk-3.0.xsd'
        );
        foreach ($this as $work) {
            assert($work instanceof Work);
            $workNode = $bulk->appendChild($dom->createElementNS(Work::$namespaceWork, 'work:work'));

            $work->addMetaToWorkNode($dom, $workNode);
        }

        return $dom->saveXML();
    }

    /**
     * @return Iterator<int, T>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->works);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->works[$offset]);
    }

    public function offsetGet(mixed $offset): ?Work
    {
        return $this->works[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->works[] = $value;
        } else {
            $this->works[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->works[$offset]);
    }
}
