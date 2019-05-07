<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentMagicGetterTest extends BaseCcdaParserTestCase
{
    public function testGetNonexistentAttribute()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
        $attributeName = 'thisAttributeDoesNotExist';
        $this->assertNull($ccdaDocument->{$attributeName});
    }

    public function testGetNamespacesAttribute()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $expected = $this->getNamespacesArrayFromSimpleXmlElement($simpleXmlMockObject);
        $this->runGetAttributeTest($simpleXmlMockObject, 'namespaces', $expected);
    }

    public function testGetSimpleXmlElementAttribute()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $this->runGetAttributeTest($simpleXmlMockObject, 'simpleXmlElement', $simpleXmlMockObject);
    }

    public function testGetElementAttributePrefixAttribute()
    {
        // This test is kind of pointless but it helps us get a higher code coverage rating
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $expected = $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefix');
        $this->runGetAttributeTest($simpleXmlMockObject, 'elementAttributePrefix', $expected);
    }

    public function testGetElementAttributePrefixDelimiterAttribute()
    {
        // This test is kind of pointless but it helps us get a higher code coverage rating
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $expected = $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefixDelimiter');
        $this->runGetAttributeTest($simpleXmlMockObject, 'elementAttributePrefixDelimiter', $expected);
    }

    public function testGetDataAttribute()
    {
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $this->runGetAttributeTest($mockData['simpleXmlElement'], 'data', $mockData['array']);
    }

    protected function runGetAttributeTest(\SimpleXMLElement $simpleXMLElement, string $attributeName, $attributeValue)
    {
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXMLElement);
        $this->assertEquals($attributeValue, $ccdaDocument->{$attributeName});
    }
}
