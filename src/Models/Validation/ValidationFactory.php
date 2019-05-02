<?php

namespace Uhin\Ccda\Models\Validation;

class ValidationFactory
{
    public static function validateSimpleXmlElement(\SimpleXMLElement $simpleXmlElement): bool
    {
        return true; // @todo Add CCDA XML Document Validation Here
    }

    protected function __construct()
    {
    }
}
