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

    public function parseElement(\SimpleXMLElement $simpleXmlElement, bool $setReturnArrayIndexToElementName = true): array
    {
        $return = [];
        $value = trim((string) $simpleXmlElement);
        $attributes = $this->parseElementAttributesIntoNamespacedArray($simpleXmlElement);
        $children = $this->parseElementChildrenIntoNamespacedArray($simpleXmlElement);
        foreach (array_keys($this->parentDocument->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces to Merge Element Data
            if (empty($currentNamespacePrefix)) { // Check for Namespaced or Global

                // Global Data
                if (!empty($attributes[$currentNamespacePrefix])) { // Check for Attributes in Namespace
                    $return = array_merge($return, $attributes[$currentNamespacePrefix]);
                } // End of Check for Attributes in Namespace
                if (!empty($children[$currentNamespacePrefix])) { // Check for Children in Namespace
                    $return = array_merge($return, $children[$currentNamespacePrefix]);
                } // End of Check for Children in Namespace

            } else { // Middle of Check for Namespaced or Global

                // Namespaced Data
                $return[$currentNamespacePrefix] = [];
                if (!empty($attributes[$currentNamespacePrefix])) { // Check for Attributes in Namespace
                    $return[$currentNamespacePrefix] = $attributes[$currentNamespacePrefix];
                } // End of Check for Attributes in Namespace
                if (!empty($children[$currentNamespacePrefix])) { // Check for Children in Namespace
                    $return[$currentNamespacePrefix] = array_merge($return[$currentNamespacePrefix], $children[$currentNamespacePrefix]);
                } // End of Check for Children in Namespace
                if (empty($return[$currentNamespacePrefix])) { // Purge Empty Namespace Containers from Return Value
                    unset($return[$currentNamespacePrefix]);
                } // End of Purge Empty Namespace Containers from Return Value

            } // End of Check for Namespaced or Global
        } // End of Loop through Namespaces to Merge Element Data
        if (!empty($value)) { // Check for Value
            $return['value'] = $value;
        } // End of Check for Value
        return $return;
    }

    protected function consolidateElementData(string $value, array $attributes): array
    {
        return array_merge();
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

    public function parseElementAttributesIntoNamespacedArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        foreach (array_keys($this->parentDocument->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces
            $return[$currentNamespacePrefix] = [];
            foreach ($simpleXmlElement->attributes($currentNamespacePrefix, true) as $currentAttribute) { // Loop through Element Attributes in Current Namespace
                $return[$currentNamespacePrefix]['attribute:' . $currentAttribute->getName()] = (string) $currentAttribute;
            } // End of Loop through Element Attributes in Current Namespace
            if (empty($return[$currentNamespacePrefix])) { // Remove Namespaces that Have No Data/Attributes
                unset($return[$currentNamespacePrefix]);
            } // End of Remove Namespaces that Have No Data/Attributes
        } // End of Loop through Namespaces
        return $return;
    }

    public function parseElementChildrenIntoNamespacedArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        foreach (array_keys($this->parentDocument->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces
            $return[$currentNamespacePrefix] = [];
            foreach ($simpleXmlElement->children($currentNamespacePrefix, true) as $currentChild) { // Loop through Element Children in Current Namespace
                $return[$currentNamespacePrefix][$currentChild->getName()] = $this->parseElement($currentChild);
            } // End of Loop through Element Children in Current Namespace
            if (empty($return[$currentNamespacePrefix])) { // Remove Namespaces that Have No Data/Attributes
                unset($return[$currentNamespacePrefix]);
            } // End of Remove Namespaces that Have No Data/Attributes
        } // End of Loop through Namespaces
        return $return;
    }

    protected function parseElementSingleAttribute(\SimpleXMLElement $simpleXmlElement, string $attributeName): string
    {
        return trim((string) $simpleXmlElement->attributes()->{$attributeName});
    }
}
