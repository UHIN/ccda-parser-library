<?php

namespace Uhin\Ccda\Models;

class CcdaDocumentHeader extends CcdaDocumentPortion
{
    protected $knownAttributes = [
//        'author',
        'code',
        'confidentialityCode',
//        'custodian',
//        'documentationOf',
        'effectiveTime',
        'id',
        'languageCode',
        'realmCode',
//        'recordTarget',
        'templateId',
        'title',
        'typeId',
    ];

    protected function get_author(): array
    {
        // @todo Insert Functionality Here
    }

    protected function get_code(): array
    {
        return $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->code);
    }

    protected function get_confidentialityCode(): array
    {
        return $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->confidentialityCode);
    }

    protected function get_custodian(): array
    {
        // @todo Insert Functionality Here
    }

    protected function get_documentationOf(): array
    {
        // @todo Insert Functionality Here
    }

    protected function get_effectiveTime(): string
    {
        return $this->parseElementSingleAttribute($this->parentDocument->simpleXmlElement->effectiveTime, 'value');
    }

    protected function get_id(): array
    {
        return $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->id);
    }

    protected function get_languageCode(): string
    {
        return $this->parseElementSingleAttribute($this->parentDocument->simpleXmlElement->languageCode, 'code');
    }

    protected function get_realmCode(): string
    {
        return $this->parseElementSingleAttribute($this->parentDocument->simpleXmlElement->realmCode, 'code');
    }

    protected function get_recordTarget(): array
    {
        // @todo Insert Functionality Here
    }

    protected function get_templateId(): array
    {
        $return = [];
        foreach ($this->parentDocument->simpleXmlElement->templateId as $currentTemplateId) { // Loop through Template ID Fields
            $return[] = $this->parseElementSingleAttribute($currentTemplateId, 'root');
        } // End of Loop through Template ID Fields
        return $return;
    }

    protected function get_title(): string
    {
        return trim((string) $this->parentDocument->simpleXmlElement->title);
    }

    protected function get_typeId(): array
    {
        return $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->typeId);
    }
}
