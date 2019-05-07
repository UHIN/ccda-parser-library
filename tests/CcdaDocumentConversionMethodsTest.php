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

    public function testConvertXmlMethodParsesXmlDataIntoArray()
    {
        /* This test is very similar to:
         * CcdaDocumentMagicGetterTest::testGetDataAttribute()
         * CcdaDocumentConversionMethodsTest::testConvertXmlMethodAppropriatelyChangesXmlDataArray()
         */
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $ccdaDocument->convertXml();
        $this->assertNotNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $this->assertEquals($mockData['array'], $ccdaDocument->data);
    }

    public function testConvertXmlMethodAppropriatelyChangesXmlDataArray()
    {
        /* This test is very similar to:
         * CcdaDocumentMagicGetterTest::testGetDataAttribute()
         * CcdaDocumentConversionMethodsTest::testConvertXmlMethodParsesXmlDataIntoArray()
         */
        $mockData = $this->getSimpleXmlElementAndDataArray();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($mockData['simpleXmlElement']);
        $this->assertNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $ccdaDocument->convertXml();
        $this->assertNotNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $this->assertEquals($mockData['array'], $ccdaDocument->data);
        $newAttributePrefix = $this->faker->word;
        $newAttributePrefixDelimiter = $this->faker->randomAscii;
        $newDataArray = $this->getDataArrayFromCcdaDocumentMockDataBuilderArray($mockData['builderArray'], $newAttributePrefix, $newAttributePrefixDelimiter);
        $ccdaDocument->elementAttributePrefix = $newAttributePrefix;
        $ccdaDocument->elementAttributePrefixDelimiter = $newAttributePrefixDelimiter;
        $ccdaDocument->convertXml();
        $this->assertNotEquals($mockData['array'], $ccdaDocument->data);
        $this->assertEquals($newDataArray, $ccdaDocument->data);
    }
}
