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
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $expected = $mockData['simpleXmlElement']->getNamespaces(true);
        if (!in_array('', $expected)) { // Verify "Global" Namespace Element Present
            $expected[''] = ''; // This is the same functionality as the constructor of the CcdaDocument object
        } // End of Verify "Global" Namespace Element Present
        $this->runGetAttributeTest($mockData['simpleXmlElement'], 'namespaces', $expected);
    }

    public function testGetSimpleXmlElementAttribute()
    {
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $this->runGetAttributeTest($mockData['simpleXmlElement'], 'simpleXmlElement', $mockData['simpleXmlElement']);
    }

    public function testGetElementAttributePrefixAttribute()
    {
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $expected = $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefix');
        $this->runGetAttributeTest($mockData['simpleXmlElement'], 'elementAttributePrefix', $expected);
    }

    public function testGetElementAttributePrefixDelimiterAttribute()
    {
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $expected = $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefixDelimiter');
        $this->runGetAttributeTest($mockData['simpleXmlElement'], 'elementAttributePrefixDelimiter', $expected);
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
