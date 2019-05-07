<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentFactoryMethodSimpleXmlElementTest extends BaseCcdaParserTestCase
{
    public function testValidSimpleXmlElementObject()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $expectedNamespaces = $this->getNamespacesArrayFromSimpleXmlElement($simpleXmlMockObject);
        try {
            $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
            $this->assertInstanceOf(CcdaDocument::class, $ccdaDocument);
            $this->assertSame($simpleXmlMockObject, $ccdaDocument->simpleXmlElement);
            $this->assertEquals($expectedNamespaces, $ccdaDocument->namespaces);
        } catch (\Exception $e) {
            $this->fail(sprintf('Unexpected exception thrown: %s(%s)', get_class($e), $e->getMessage()));
        }
    }
}
