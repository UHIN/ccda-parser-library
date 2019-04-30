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
        $return = $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->recordTarget);

        $return['patientRole'] = $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->recordTarget->patientRole);

        if ($this->parentDocument->simpleXmlElement->recordTarget->patientRole->id->count()) { // Check for Patient Role ID Entries
            $return['patientRole']['id'] = [];
            foreach ($this->parentDocument->simpleXmlElement->recordTarget->patientRole->id as $currentPatientRoleId) { // Loop through Patient Role ID Entries
                $return['patientRole']['id'][] = $this->parseElementAttributesIntoArray($currentPatientRoleId);
            } // End of Loop through Patient Role ID Entries
            unset($currentPatientRoleId);
        } // End of Check for Patient Role ID Entries

        if (isset($this->parentDocument->simpleXmlElement->recordTarget->patientRole->addr)) { // Check for Patient Role Address
            $return['patientRole']['addr'] = $this->parseElementAttributesIntoArray($this->parentDocument->simpleXmlElement->recordTarget->patientRole->addr);
            foreach ($this->parentDocument->simpleXmlElement->recordTarget->patientRole->addr->children() as $currentAddressPart) { // Loop through Address Parts
                $return['patientRole']['addr'][$currentAddressPart->getName()] = trim((string) $currentAddressPart);
            } // End of Loop through Address Parts
        } // End of Check for Patient Role Address

        return $return;
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
