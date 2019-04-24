<?php

namespace Uhin\Ccda\Models;

class CcdaDocumentBody extends CcdaDocumentPortion
{
    protected $knownAttributes = [
        'component',
    ];

    protected function get_component(): array
    {
        return $this->parseElementIntoComponentsArray($this->parentDocument->simpleXmlElement->component);
    }
}
