<?php

namespace Uhin\Ccda\Tests;

use Uhin\Ccda\Exceptions\IllegalOperation;
use Uhin\Ccda\Models\CcdaDocument;

class CcdaDocumentMagicSetterTest extends BaseCcdaParserTestCase
{
    public function testSetNonexistentAttribute()
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
        $attributeName = 'thisAttributeDoesNotExist';
        $attributeValue = $this->faker->text;
        try {
            $ccdaDocument->{$attributeName} = $attributeValue;
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\IllegalOperation, Actual: [none]');
        } catch (IllegalOperation $e) {
            $this->assertEquals(sprintf('%s does not support setting this attribute: %s', CcdaDocument::class, $attributeName), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\IllegalOperation, Actual: %s', get_class($e)));
        }
    }

    public function testSetSimpleXmlElementAttribute()
    {
        $this->runSetUnsettableAttributeTest('simpleXmlElement');
    }

    public function testSetNamespacesAttribute()
    {
        $this->runSetUnsettableAttributeTest('namespaces');
    }

    public function testSetDataAttribute()
    {
        $this->runSetUnsettableAttributeTest('data');
    }

    public function testSetElementAttributePrefixAttribute()
    {
        $this->runSetSettableAttributeTest('elementAttributePrefix');
    }

    public function testSetElementAttributePrefixDelimiterAttribute()
    {
        $this->runSetSettableAttributeTest('elementAttributePrefixDelimiter');
    }

    protected function runSetSettableAttributeTest(string $attributeName)
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
        $attributeValue = $this->faker->word;
        $dataAttributeBeforeSetting = $ccdaDocument->data;
        $this->assertNotNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $this->assertNotEmpty($dataAttributeBeforeSetting);
        $this->assertNotEquals($attributeValue, $ccdaDocument->{$attributeName});
        $ccdaDocument->{$attributeName} = $attributeValue;
        $this->assertEquals($attributeValue, $ccdaDocument->{$attributeName});
        $this->assertNull($this->getRestrictedObjectProperty($ccdaDocument, 'data'));
        $this->assertNotEquals($dataAttributeBeforeSetting, $ccdaDocument->data);
    }

    protected function runSetUnsettableAttributeTest(string $attributeName)
    {
        $simpleXmlMockObject = $this->getSimpleXmlElement();
        $ccdaDocument = CcdaDocument::getDocumentFromSimpleXmlElement($simpleXmlMockObject);
        $attributeValue = $this->faker->text;
        try {
            $ccdaDocument->{$attributeName} = $attributeValue;
            $this->fail('No exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\IllegalOperation, Actual: [none]');
        } catch (IllegalOperation $e) {
            $this->assertEquals(sprintf('%s does not support setting this attribute: %s', CcdaDocument::class, $attributeName), $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(sprintf('Incorrect exception thrown. Expected: \\Uhin\\Ccda\\Exceptions\\IllegalOperation, Actual: %s', get_class($e)));
        }
    }
}
