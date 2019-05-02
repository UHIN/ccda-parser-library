<?php

namespace Uhin\Ccda\Models\Parsing;

class ParserFactory
{
    public static function getParserFunctionsForSimpleXmlElement(\SimpleXMLElement $simpleXmlElement): array
    {
        // @todo Use Passed SimpleXMLElement Parameter to Determine Which Closures to Return

        return [
            'attributes'    => static::getDefaultElementAttributesParser(),
            'children'      => static::getDefaultElementChildrenParser(),
        ];
    }

    protected function __construct()
    {
    }

    protected static function getDefaultElementAttributesParser(): callable
    {
        return function(\SimpleXMLElement $simpleXmlElement): array {
            // @todo Put Logic from CcdaDocument::parseElementAttributesIntoNamespacedArray() Here
        };
    }

    protected static function getDefaultElementChildrenParser(): callable
    {
        return function(\SimpleXMLElement $simpleXmlElement): array {
            // @todo Put Logic from CcdaDocument::parseElementChildrenIntoNamespacedArray() Here
        };
    }
}
