<?php

namespace Orcid\Work;

use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use Orcid\Work\ExternalId\ExternalId;
use RuntimeException;

use function is_array;

trait XMLWorkGeneration
{
    /**
     * @throws DOMException
     */
    public function addMetaToWorkNode(DOMDocument $dom, DOMElement|DOMNode $work): DOMNode
    {
        $this->checkMetaValueAndThrowExceptionIfNecessary();

        if ($this->putCode() !== null) {
            $work->setAttribute('put-code', (int) $this->putCode());
        }

        //add work title
        $workTitle = $work->appendChild($dom->createElementNS(self::$namespaceWork, 'title'));
        $title = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon, 'title'));
        $title->appendChild($dom->createCDATASection($this->title()));

        if ($this->subtitle() !== null) {
            $subtitle = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon, 'subtitle'));
            $subtitle->appendChild($dom->createCDATASection($this->subtitle()));
        }

        //translatedTitleLanguageCode is required to send translatedTitle
        if ($this->translatedTitle() !== null && $this->translatedTitleLanguageCode() !== null) {
            $translatedTitle = $workTitle->appendChild(
                $dom->createElementNS(self::$namespaceCommon, 'translated-title')
            );
            $translatedTitle->appendChild($dom->createCDATASection($this->translatedTitle()));
            $translatedTitle->setAttribute('language-code', $this->translatedTitleLanguageCode()->value);
        }

        if ($this->journalTitle() !== null) {
            $journalTitle = $work->appendChild($dom->createElementNS(self::$namespaceWork, 'journal-title'));
            $journalTitle->appendChild($dom->createCDATASection($this->journalTitle()));
        }

        if ($this->shortDescription() !== null) {
            $shortDescription = $work->appendChild($dom->createElementNS(self::$namespaceWork, 'short-description'));
            $shortDescription->appendChild($dom->createCDATASection($this->shortDescription()));
        }

        if ($this->citation() !== null) {
            $work->appendChild($this->nodeCitation($dom, $this->citationType(), $this->citation()));
        }

        // add work Type
        $work->appendChild($dom->createElementNS(self::$namespaceWork, 'type', $this->type()->value));

        // add publication date
        if ($this->publicationDate() !== null) {
            $work->appendChild($this->dateNode($dom, $this->publicationDate()));
        }

        //add external ident
        $externalIds = $work->appendChild($dom->createElementNS(self::$namespaceCommon, 'external-ids'));
        foreach ($this->externals() as $externalId) {
            $externalIds->appendChild($this->externalIdNode($dom, $externalId));
        }

        if ($this->url() !== null) {
            $work->appendChild($dom->createElementNS(self::$namespaceWork, 'url', $this->url()));
        }

        //add authors
        $contributors = $this->contributors();
        $principal_authors = $this->principalAuthors();
        if (is_array($contributors) || is_array($principal_authors)) {
            $contributors_node = $work->appendChild($dom->createElementNS(self::$namespaceWork, 'contributors'));
            foreach ($contributors as $contributor) {
                $contributors_node->appendChild($this->nodeContributor($dom, $contributor));
            }

            foreach ($principal_authors as $contributor) {
                $contributors_node->appendChild($this->nodeContributor($dom, $contributor));
            }
        }

        if ($this->languageCode() !== null) {
            $work->appendChild($dom->createElementNS(self::$namespaceCommon, 'language-code', $this->languageCode()->value));
        }

        if ($this->country() !== null) {
            $work->appendChild($dom->createElementNS(self::$namespaceCommon, 'country', $this->country()->value));
        }

        return $work;
    }

    /**
     * Build an external identifier node
     * @throws DOMException
     */
    protected function externalIdNode(DOMDocument $dom, ExternalId $externalId): DOMNode {
        $externalIdNode = $dom->createElementNS(self::$namespaceCommon, 'external-id');

        //Type Node
        $externalIdTypeNode = $dom->createElementNS(self::$namespaceCommon, 'external-id-type');
        $externalIdTypeNodeValue = $dom->createTextNode($externalId->type->value);
        $externalIdTypeNode->appendChild($externalIdTypeNodeValue);
        $externalIdNode->appendChild($externalIdTypeNode);

        // Value Node
        $externalIdValueNode = $dom->createElementNS(self::$namespaceCommon, 'external-id-value');
        $externalIdValueNodeValue = $dom->createTextNode($externalId->value);
        $externalIdValueNode->appendChild($externalIdValueNodeValue);
        $externalIdNode->appendChild($externalIdValueNode);

        if (!empty($externalId->url)) {
            //url Node
            $externalIdUrlNode = $dom->createElementNS(self::$namespaceCommon, 'external-id-url');
            $externalIdUrlNodeValue = $dom->createTextNode($externalId->url);
            $externalIdUrlNode->appendChild($externalIdUrlNodeValue);
            $externalIdNode->appendChild($externalIdUrlNode);
        }

        $externalIdNode->appendChild(
            $dom->createElementNS(self::$namespaceCommon, 'external-id-relationship', $externalId->relationship->value)
        );

        return $externalIdNode;
    }

    /**
     * Build an author node
     *
     * @throws DOMException
     */
    protected function nodeContributor(DOMDocument $dom, Contributor $contributor): DOMElement|false
    {
        $contributor_node = $dom->createElementNS(self::$namespaceWork, 'contributor');
        $orcid = $contributor->orcid();
        if (!empty($orcid)) {
            $contributorOrcid = $contributor_node->appendChild(
                $dom->createElementNS(self::$namespaceCommon, 'contributor-orcid')
            );
            $env = $contributor->sandbox() ? 'sandbox.' : '';
            $contributorOrcid->appendChild(
                $dom->createElementNS(
                    self::$namespaceCommon,
                    'uri',
                    "https://{$env}orcid.org/$orcid"
                )
            );
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon, 'path', $orcid));
            $contributorOrcid->appendChild(
                $dom->createElementNS(self::$namespaceCommon, 'host', "{$env}orcid.org")
            );
        }
        $creditName = $contributor_node->appendChild($dom->createElementNS(self::$namespaceWork, 'credit-name'));
        $creditName->appendChild($dom->createCDATASection($contributor->creditName()));
        $attributes = $contributor_node->appendChild($dom->createElementNS(self::$namespaceWork, 'contributor-attributes'));
        if ($contributor->sequence() !== null) {
            $attributes->appendChild($dom->createElementNS(self::$namespaceWork, 'contributor-sequence', $contributor->sequence()->value));
        }
        $attributes->appendChild($dom->createElementNS(self::$namespaceWork, 'contributor-role', $contributor->role()->value));
        return $contributor_node;
    }

    /**
     * Build a citation node
     * @throws DOMException
     */
    protected function nodeCitation(DOMDocument $dom, CitationType $type, string $value): DOMNode
    {
        $citation = $dom->createElementNS(self::$namespaceWork, 'citation');
        $citation->appendChild($dom->createElementNS(self::$namespaceWork, 'citation-type', $type->value));
        $citationValue = $dom->createElementNS(self::$namespaceWork, 'citation-value');
        $citationValue->appendChild($dom->createTextNode($value));
        $citation->appendChild($citationValue);
        return $citation;
    }

    /**
     * Build a date Node
     *
     * @throws DOMException
     */
    protected function dateNode(DOMDocument $dom, Carbon $date): DOMNode
    {
        $validDateMonth = false;
        $publicationDate = $dom->createElementNS(self::$namespaceCommon, 'publication-date');

        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');

        $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, 'year', $year));

        if ($month !== '') {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, 'month', $month));
            $validDateMonth = true;
        }

        if ($day !== '' && $validDateMonth) {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, 'day', $day));
        }

        return $publicationDate;
    }


    /**
     * @throws DOMException
     */
    public function getXMLData(): false|string
    {
        $dom = self::getNewOrcidCommonDomDocument();
        $workNode = $dom->appendChild($dom->createElementNS(self::$namespaceWork, 'work:work'));
        $dom->createAttributeNS(self::$namespaceCommon, 'common:common');
        $workNode->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            self::$namespaceWork . '/ work-2.0.xsd '
        );
        $this->addMetaToWorkNode($dom, $workNode);
        return $dom->saveXML();
    }

    public static function getNewOrcidCommonDomDocument(bool $preserve_white_space = false, bool $format_output = true): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = $preserve_white_space;
        $dom->formatOutput = $format_output;
        return $dom;
    }

    /**
     * @throws RuntimeException
     */
    public function checkMetaValueAndThrowExceptionIfNecessary(): void
    {
        $response = '';
        if (empty($this->title)) {
            $response .= ' Title recovery failed: Title value cannot be empty';
        }
        if (empty($this->type)) {
            $response .= ' Work Type recovery failed: Type value cannot be empty';
        }
        if (empty($this->externals)) {
            $response .= ' externals Ident recovery failed: externals values cannot be empty';
        }
        if ($response !== '') {
            throw new RuntimeException($response);
        }
    }
}
