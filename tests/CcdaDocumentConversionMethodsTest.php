<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentConversionMethodsTest extends BaseCcdaParserTestCase
{
    public function testToArrayMethod()
    {
        // This test is very similar to CcdaDocumentMagicGetterTest::testGetDataAttribute()
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertEquals($mockData['array'], $ccdaDocument->toArray());
    }

    public function testToStdClassMethod()
    {
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertEquals((object) $mockData['array'], $ccdaDocument->toStdClass());
    }

    public function testToJsonMethod()
    {
        // This test is very similar to CcdaDocumentConversionMethodsTest::testToStringMethod()
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertEquals(json_encode($mockData['array']), $ccdaDocument->toJson());
    }

    public function testToStringMethod()
    {
        // This test is very similar to CcdaDocumentConversionMethodsTest::testToJsonMethod()
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertEquals(json_encode($mockData['array']), $ccdaDocument->__toString());
    }
}
