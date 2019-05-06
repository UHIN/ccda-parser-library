<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Exceptions\InvalidSourceXmlData;
use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentFactoryMethodXmlStringTest extends BaseCcdaParserTestCase
{
    public function testEmptyString()
    {
        try {
            CcdaDocument::getDocumentFromXmlString('');
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: [none]');
        } catch (InvalidSourceXmlData $e) {
            $this->assertEquals('Invalid source XML string: [empty]', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: %s', get_class($e)));
        }
    }

    public function testInvalidXmlString()
    {
        $invalidXmlString = $this->getInvalidXmlString();
        try {
            CcdaDocument::getDocumentFromXmlString($invalidXmlString);
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: [none]');
        } catch (InvalidSourceXmlData $e) {
            $this->assertEquals(sprintf('Invalid source XML string: %s', $invalidXmlString), $e->getMessage());
        } catch (\Exception $e) {

            echo(sprintf("\nUnexpected Exception: %s(%s)\n", get_class($e), $e->getMessage())); // @todo Delete This

            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: %s', get_class($e)));
        }
    }

    public function testValidXmlString()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        try {
            $ccdaDocument = CcdaDocument::getDocumentFromXmlString($simpleXmlMockObject->asXML());
            $this->assertInstanceOf(CcdaDocument::class, $ccdaDocument);
            $this->assertEquals($simpleXmlMockObject, $ccdaDocument->simpleXmlElement);
        } catch (\Exception $e) {
            $this->fail(sprintf('Unexpected exception thrown: %s(%s)', get_class($e), $e->getMessage()));
        }
    }
}