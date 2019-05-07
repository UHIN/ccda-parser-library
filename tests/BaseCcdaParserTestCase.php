<?php

namespace Uhin\Ccda\Tests;

use Faker\Factory as FakerFactory;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Uhin\Ccda\Models\CcdaDocument;

abstract class BaseCcdaParserTestCase extends TestCase
{
    use PHPMock;

    /**
     * The fake data generator.
     *
     * @var \Faker\Generator
     * @see BaseCcdaParserTestCase::__construct()
     */
    protected $faker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = FakerFactory::create();
    }

    /**
     * Generates a random (fake) string to be used as an invalid XML string.
     *
     * @return string
     * @see Faker\Provider\Lorem::text()
     */
    protected function getInvalidXmlString(): string
    {
        return $this->faker->text;
    }

    /**
     * Generates a random (fake) string that is valid XML.
     *
     * @return string
     * @throws \ReflectionException
     * @see BaseCcdaParserTestCase::getSimpleXmlElement()
     * @see \SimpleXMLElement::asXML()
     */
    protected function getValidXmlString(): string
    {
        return $this->getSimpleXmlElement()->asXML();
    }

    /**
     * Returns an array that can be used to build SimpleXMLElement objects for testing as well as the data array.
     *
     * @return array
     * @see BaseCcdaParserTestCase::getSimpleXmlElement()
     * @see BaseCcdaParserTestCase::getSimpleXmlElementAndDataArray()
     * @see BaseCcdaParserTestCase::getDataArrayFromCcdaDocumentMockDataBuilderArray()
     */
    protected function getCcdaDocumentMockDataBuilderArray(): array
    {
        return [
            'topLevelElementName'           => $this->faker->word,
            'globalAttributeName'           => $this->faker->word,
            'globalAttributeValue'          => $this->faker->text,
            'globalChildName'               => $this->faker->word,
            'globalChildValue'              => $this->faker->text,
            'namespaceName'                 => $this->faker->word,
            'namespaceUrl'                  => 'http://www.w3.org/2001/XMLSchema-instance',
            'namespacedAttributeName'       => $this->faker->word,
            'namespacedAttributeValue'      => $this->faker->text,
            'namespacedChildName'           => $this->faker->word,
            'namespacedChildValue'          => $this->faker->text,
        ];
    }

    /**
     * Returns a SimpleXMLElement based on a mock data builder array.
     *
     * @param array $mockData
     * @return \SimpleXMLElement
     * @see BaseCcdaParserTestCase::getCcdaDocumentMockDataBuilderArray()
     * @see \SimpleXMLElement::addAttribute()
     * @see \SimpleXMLElement::addChild()
     * @see BaseCcdaParserTestCase::getSimpleXmlElementAndDataArray()
     * @see BaseCcdaParserTestCase::getSimpleXmlElement()
     */
    protected function getSimpleXmlElementFromCcdaDocumentMockDataBuilderArray(array $mockData): \SimpleXMLElement
    {
        $return = new \SimpleXMLElement(sprintf('<%s />', $mockData['topLevelElementName']));
        $return->addAttribute($mockData['globalAttributeName'], $mockData['globalAttributeValue']);
        $return->addChild($mockData['globalChildName'], $mockData['globalChildValue']);
        $return->addAttribute(sprintf('%s:%s', $mockData['namespaceName'], $mockData['namespacedAttributeName']), $mockData['namespacedAttributeValue'], $mockData['namespaceUrl']);
        $return->addChild(sprintf('%s:%s', $mockData['namespaceName'], $mockData['namespacedChildName']), $mockData['namespacedChildValue'], $mockData['namespaceUrl']);
        return $return;
    }

    /**
     * Returns a data array based on a mock data builder array.
     *
     * @param array $mockData
     * @param string|null $elementAttributePrefix
     * @param string|null $elementAttributePrefixDelimiter
     * @return array
     * @throws \ReflectionException
     * @see BaseCcdaParserTestCase::getCcdaDocumentMockDataBuilderArray()
     * @see BaseCcdaParserTestCase::getSimpleXmlElementAndDataArray()
     */
    protected function getDataArrayFromCcdaDocumentMockDataBuilderArray(array $mockData, string $elementAttributePrefix = null, string $elementAttributePrefixDelimiter = null): array
    {
        $elementAttributePrefix = (!empty($elementAttributePrefix) ? $elementAttributePrefix : $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefix'));
        $elementAttributePrefixDelimiter = (!empty($elementAttributePrefixDelimiter) ? $elementAttributePrefixDelimiter : $this->getRestrictedObjectPropertyDefaultValue(CcdaDocument::class, 'elementAttributePrefixDelimiter'));

        /* This array is created in a specific order so that the unit tests will pass.  Specifically:
         * CcdaDocumentConversionMethodsTest::testToJsonMethod()
         * CcdaDocumentConversionMethodsTest::testToStringMethod()
         */
        $return = [$mockData['topLevelElementName'] => [
        $mockData['namespaceName'] => [
                sprintf('%s%s%s', $elementAttributePrefix, $elementAttributePrefixDelimiter, $mockData['namespacedAttributeName']) => $mockData['namespacedAttributeValue'],
                $mockData['namespacedChildName'] => ['value' => $mockData['namespacedChildValue']],
            ],
            sprintf('%s%s%s', $elementAttributePrefix, $elementAttributePrefixDelimiter, $mockData['globalAttributeName']) => $mockData['globalAttributeValue'],
            $mockData['globalChildName'] => ['value' => $mockData['globalChildValue']],
        ]];

        return $return;
    }

    /**
     * Generates a random (fake) SimpleXMLElement object and a corresponding array to how it should be parsed.
     *
     * @return array
     * @throws \ReflectionException
     * @see BaseCcdaParserTestCase::getCcdaDocumentMockDataBuilderArray()
     * @see BaseCcdaParserTestCase::getDataArrayFromCcdaDocumentMockDataBuilderArray()
     * @see BaseCcdaParserTestCase::getSimpleXmlElementFromCcdaDocumentMockDataBuilderArray()
     */
    protected function getSimpleXmlElementAndDataArray(): array
    {
        return [
            'builderArray'      => $builderArray = $this->getCcdaDocumentMockDataBuilderArray(),
            'array'             => $this->getDataArrayFromCcdaDocumentMockDataBuilderArray($builderArray),
            'simpleXmlElement'  => $this->getSimpleXmlElementFromCcdaDocumentMockDataBuilderArray($builderArray),
        ];
    }

    /**
     * Generates a random (fake) SimpleXMLElement object.
     *
     * @return \SimpleXMLElement
     * @throws \ReflectionException
     * @see BaseCcdaParserTestCase::getCcdaDocumentMockDataBuilderArray()
     * @see BaseCcdaParserTestCase::getSimpleXmlElementFromCcdaDocumentMockDataBuilderArray()
     */
    protected function getSimpleXmlElement(): \SimpleXMLElement
    {
        return $this->getSimpleXmlElementFromCcdaDocumentMockDataBuilderArray($this->getCcdaDocumentMockDataBuilderArray());
    }

    /**
     * Unit test helper method that uses reflection to get the value of a restricted object property.
     *
     * @param object $object
     * @param string $attributeName
     * @return mixed
     * @throws \ReflectionException
     * @see \ReflectionProperty::setAccessible()
     * @see \ReflectionProperty::getValue()
     */
    protected function getRestrictedObjectProperty(object $object, string $attributeName)
    {
        $reflectionAttribute = new \ReflectionProperty(get_class($object), $attributeName);
        $reflectionAttribute->setAccessible(true);
        return $reflectionAttribute->getValue($object);
    }

    /**
     * Unit test helper method that uses reflection to get the default value of a restricted object property.
     *
     * @param string $className
     * @param string $attributeName
     * @return mixed
     * @throws \ReflectionException
     * @see \ReflectionClass::getDefaultProperties()
     */
    protected function getRestrictedObjectPropertyDefaultValue(string $className, string $attributeName)
    {
        $reflectionClass = new \ReflectionClass($className);
        return $reflectionClass->getDefaultProperties()[$attributeName];
    }

    /**
     * Unit test helper method to construct a namespaces array from the SimpleXMLElement object that is identical to
     * what the CcdaDocument uses.
     *
     * @param \SimpleXMLElement $simpleXmlElement
     * @return array
     * @see \SimpleXMLElement::getNamespaces()
     */
    protected function getNamespacesArrayFromSimpleXmlElement(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = $simpleXmlElement->getNamespaces(true);
        /* The CcdaDocument object manually adds in a "global" namespace element, if it is not present in the source
         * XML; so we will mimic this behavior for proper assertions against this object.
         */
        if (!in_array('', $return)) { // Verify "Global" Namespace Element Present
            $return[''] = '';
        } // End of Verify "Global" Namespace Element Present
        return $return;
    }
}
