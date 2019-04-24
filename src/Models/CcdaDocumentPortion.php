<?php

namespace Uhin\Ccda\Models;

use Uhin\Ccda\Exceptions\IllegalOperation;

abstract class CcdaDocumentPortion
{
    protected $parentDocument;

    protected $data = [];

    protected $knownAttributes = [];

    public function __construct(CcdaDocument $parentDocument)
    {
        $this->parentDocument = $parentDocument;
    }

    public function __get(string $attributeName)
    {
        $return = null;
        if (array_key_exists($attributeName, $this->data)) { // Look for Attribute in Known Access Points
            $return = $this->data[$attributeName];
        } elseif (in_array($attributeName, $this->knownAttributes) && method_exists($this, 'get_' . $attributeName)) { // Middle of Look for Attribute in Known Access Points
            $return = $this->data[$attributeName] = $this->{'get_' . $attributeName}();
        } // End of Look for Attribute in Known Access Points
        return $return;
    }

    public function __set(string $attributeName, $attributeValue)
    {
        throw new IllegalOperation(sprintf('%s does not support setting values (%s)', get_called_class(), $attributeName));
    }

    public function toArray(): array
    {
        $this->loadAllValues();
        return $this->data;
    }

    protected function loadAllValues(): void
    {
        foreach ($this->knownAttributes as $currentKnownAttribute) { // Loop through Known Attributes
            if (!array_key_exists($currentKnownAttribute, $this->data) && method_exists($this, 'get_' . $currentKnownAttribute)) { // Check for Uncached Retrievable Attributes
                $this->data[$currentKnownAttribute] = $this->{'get_' . $currentKnownAttribute}();
            } // End of Check for Uncached Retrievable Attributes
        } // End of Loop through Known Attributes
    }

    public function isKnownAttribute(string $attributeName): bool
    {
        return in_array($attributeName, $this->knownAttributes);
    }

    protected function parseElementIntoComponentsArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        $attributesArray = $this->parseElementAttributesIntoArray($simpleXmlElement);
        if (count($attributesArray)) { // Check for Parsed Attributes
            $return['attributes'] = $attributesArray;
        } // End of Check for Parsed Attributes
        unset($attributesArray);
        $elementValue = trim((string) $simpleXmlElement);
        if (!empty($elementValue)) { // Check for Element Value
            $return['value'] = $elementValue;
        } // End of Check for Element Value
        unset($elementValue);
        if ($simpleXmlElement->count()) { // Check for Child Elements
            $return['children'] = [];
            foreach ($simpleXmlElement->children() as $currentchild) { // Loop through Child Elements
                $return['children'][] = $this->parseElementIntoComponentsArray($currentchild);
            } // End of Loop through Child Elements
        } // End of Check for Child Elements
        return $return;
    }

    protected function parseElementAttributesIntoArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        foreach ($simpleXmlElement->attributes() as $currentAttributeName => $currentAttributeValue) { // Loop through Attributes
            $return[$currentAttributeName] = (string) $currentAttributeValue;
        } // End of Loop through Attributes
        return $return;
    }

    protected function parseElementSingleAttribute(\SimpleXMLElement $simpleXmlElement, string $attributeName): string
    {
        return trim((string) $simpleXmlElement->attributes()->{$attributeName});
    }
}
