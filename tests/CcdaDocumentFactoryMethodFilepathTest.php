<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Exceptions\InvalidParameter;
use Uhin\Ccda\Exceptions\InvalidSourceXmlData;
use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentFactoryMethodFilepathTest extends BaseCcdaParserTestCase
{
    public function testInvalidFilePath()
    {
        $filepath = $this->faker->word;
        $isFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_file');
        $isFile->expects($this->once())->with($filepath)->willReturn(false);
        try {
            CcdaDocument::getDocumentFromFilepath($filepath);
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidParameter, Actual: [none]');
        } catch (InvalidParameter $e) {
            $this->assertEquals(sprintf('File not found: %s', $filepath), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidParameter, Actual: %s', get_class($e)));
        }
    }

    public function testUnreadableFilePath()
    {
        $filepath = $this->faker->word;
        $isFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_file');
        $isFile->expects($this->once())->with($filepath)->willReturn(true);
        $isReadable = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_readable');
        $isReadable->expects($this->once())->with($filepath)->willReturn(false);
        try {
            CcdaDocument::getDocumentFromFilepath($filepath);
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidParameter, Actual: [none]');
        } catch (InvalidParameter $e) {
            $this->assertEquals(sprintf('File not readable: %s', $filepath), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidParameter, Actual: %s', get_class($e)));
        }
    }

    public function testInvalidXmlInFilepath()
    {
        $filepath = $this->faker->word;
        $isFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_file');
        $isFile->expects($this->once())->with($filepath)->willReturn(true);
        $isReadable = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_readable');
        $isReadable->expects($this->once())->with($filepath)->willReturn(true);
        $simpleXmlLoadFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'simplexml_load_file');
        $simpleXmlLoadFile->expects($this->once())->with($filepath)->willReturn(false);
        try {
            CcdaDocument::getDocumentFromFilepath($filepath);
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: [none]');
        } catch (InvalidSourceXmlData $e) {
            $this->assertEquals(sprintf('Invalid source XML file: %s', $filepath), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\InvalidSourceXmlData, Actual: %s', get_class($e)));
        }
    }

    public function testValidXmlInFilepath()
    {
        $filepath = $this->faker->word;
        $isFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_file');
        $isFile->expects($this->once())->with($filepath)->willReturn(true);
        $isReadable = $this->getFunctionMock('Uhin\\Ccda\\Models', 'is_readable');
        $isReadable->expects($this->once())->with($filepath)->willReturn(true);
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $simpleXmlLoadFile = $this->getFunctionMock('Uhin\\Ccda\\Models', 'simplexml_load_file');
        $simpleXmlLoadFile->expects($this->once())->with($filepath)->willReturn($simpleXmlMockObject);
        try {
            $ccdaDocument = CcdaDocument::getDocumentFromFilepath($filepath);
            $this->assertInstanceOf(CcdaDocument::class, $ccdaDocument);
            $this->assertSame($simpleXmlMockObject, $ccdaDocument->simpleXmlElement);
        } catch (\Exception $e) {
            $this->fail(sprintf('Unexpected exception thrown: %s(%s)', get_class($e), $e->getMessage()));
        }
    }
}
