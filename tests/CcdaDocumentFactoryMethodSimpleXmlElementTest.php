<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentFactoryMethodSimpleXmlElementTest extends BaseCcdaParserTestCase
{
    public function testValidSimpleXmlElementObject()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        try {
            $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
            $this->assertInstanceOf(CcdaDocument::class, $ccdaDocument);
            $this->assertSame($simpleXmlMockObject, $ccdaDocument->simpleXmlElement);
        } catch (\Exception $e) {
            $this->fail(sprintf('Unexpected exception thrown: %s(%s)', get_class($e), $e->getMessage()));
        }
    }
}
