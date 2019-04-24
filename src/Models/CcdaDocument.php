<?php

namespace Uhin\Ccda\Models;

use Uhin\Ccda\Exceptions\IllegalOperation;
use Uhin\Ccda\Exceptions\InvalidSourceXmlData;

class CcdaDocument
{
    public static function getDocumentFromSimpleXmlElement(\SimpleXMLElement $simpleXMLElement): CcdaDocument
    {
        if (static::validateSimpleXmlElement($simpleXMLElement)) { // Validate Passed SimpleXMLElement Object
            return new static($simpleXMLElement);
        } // End of Validate Passed SimpleXMLElement Object
    }

    public static function getDocumentFromFilepath(string $filepath): CcdaDocument
    {
        $simpleXmlElement = simplexml_load_file($filepath);
        if ($simpleXmlElement instanceof \SimpleXMLElement) { // Verify SimpleXMLElement Object Created
            return static::getDocumentFromSimpleXmlElement($simpleXmlElement);
        } else { // Middle of Verify SimpleXMLElement Object Created
            throw new InvalidSourceXmlData('Invalid source XML file: ' . $filepath);
        } // End of Verify SimpleXMLElement Object Created
    }

    public static function getDocumentFromXmlString(string $xmlString): CcdaDocument
    {
        $simpleXmlElement = simplexml_load_string($xmlString);
        if ($simpleXmlElement instanceof \SimpleXMLElement) { // Verify SimpleXMLElement Object Created
            return static::getDocumentFromSimpleXmlElement($simpleXmlElement);
        } else { // Middle of Verify SimpleXMLElement Object Created
            throw new InvalidSourceXmlData('Invalid source XML string: ' . $xmlString);
        } // End of Verify SimpleXMLElement Object Created
    }

    protected function validateSimpleXmlElement(\SimpleXMLElement $simpleXmlElement): bool
    {
        return true; // @todo Add CCDA XML Document Validation Here
    }

    protected $simpleXmlElement;

    protected $header;

    protected $body;

    protected function __construct(\SimpleXMLElement $simpleXmlElement)
    {
        $this->simpleXmlElement = $simpleXmlElement;
        $this->header = new CcdaDocumentHeader($this);
        $this->body = new CcdaDocumentBody($this);
    }

    public function toArray(): array
    {
        return array_merge($this->header->toArray(), $this->body->toArray());
    }

    public function toStdClass(): \stdClass
    {
        return (object) $this->toArray();
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function __toString(): string
    {
        return $this->toJsonString();
    }

    public function __get(string $attributeName)
    {
        switch ($attributeName) { // Check Attribute Name Parameter

            case 'simpleXmlElement':
            case 'header':
            case 'body':
                $return = $this->{$attributeName};
                break;

            default:
                if ($this->header->isKnownAttribute($attributeName)) { // Check if Getting Known Value
                    $return = $this->header->{$attributeName};
                } elseif ($this->body->isKnownAttribute($attributeName)) { // Middle of Check if Getting Known Value
                    $return = $this->body->{$attributeName};
                } else { // Middle of Check if Getting Known Value
                    $return = null;
                } // End of Check if Getting Known Value
                break;

        } // End of Check Attribute Name Parameter
        return $return;
    }

    public function __set(string $attributeName, $attributeValue)
    {
        throw new IllegalOperation(sprintf('%s does not support setting values (%s)', get_called_class(), $attributeName));
    }
}
