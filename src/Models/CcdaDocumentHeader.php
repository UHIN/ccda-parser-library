<?php

namespace Uhin\Ccda\Models;

class CcdaDocumentHeader extends CcdaDocumentPortion
{
    protected $knownAttributes = [
        'code',
        'effectiveTime',
        'id',
        'realmCode',
        'templateId',
        'title',
        'typeId',
    ];

    protected function get_code(): array
    {
        return [
            'code'          => trim((string) $this->parentDocument->simpleXmlElement->code->attributes()->code),
            'codeSystem'    => trim((string) $this->parentDocument->simpleXmlElement->code->attributes()->codeSystem),
            'displayName'   => trim((string) $this->parentDocument->simpleXmlElement->code->attributes()->displayName),
        ];
    }

    protected function get_effectiveTime(): string
    {
        return trim((string) trim((string) $this->parentDocument->simpleXmlElement->effectiveTime->attributes()->value));
    }

    protected function get_id(): array
    {
        return [
            'extension'     => trim((string) $this->parentDocument->simpleXmlElement->id->attributes()->extension),
            'root'          => trim((string) $this->parentDocument->simpleXmlElement->id->attributes()->root),
        ];
    }

    protected function get_realmCode(): string
    {
        return trim((string) $this->parentDocument->simpleXmlElement->realmCode->attributes()->code);
    }

    protected function get_templateId(): array
    {
        $return = [];
        foreach ($this->parentDocument->simpleXmlElement->templateId as $currentTemplateId) { // Loop through Template ID Fields
            $return[] = trim((string) $currentTemplateId->attributes()->root);
        } // End of Loop through Template ID Fields
        return $return;
    }

    protected function get_title(): string
    {
        return trim((string) $this->parentDocument->simpleXmlElement->title);
    }

    protected function get_typeId(): array
    {
        return [
            'extension'     => trim((string) $this->parentDocument->simpleXmlElement->typeId->attributes()->extension),
            'root'          => trim((string) $this->parentDocument->simpleXmlElement->typeId->attributes()->root),
        ];
    }
}
