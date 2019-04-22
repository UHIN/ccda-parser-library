<?php

namespace Uhin\Ccda\Models;

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
}
