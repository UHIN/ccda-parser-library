<?php

namespace Uhin\Ccda\Tests;

use Faker\Factory as FakerFactory;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;

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
     * @see BaseCcdaParserTestCase::getSimpleXmlElement()
     * @see \SimpleXMLElement::asXML()
     */
    protected function getValidXmlString(): string
    {
        return $this->getSimpleXmlElement()->asXML();
    }

    /**
     * Generates a random (fake) SimpleXMLElement object.
     *
     * @return \SimpleXMLElement
     */
    protected function getSimpleXmlElement(): \SimpleXMLElement
    {
        $simpleXmlElement = new \SimpleXMLElement(sprintf('<%s />', $this->faker->word));
        $simpleXmlElement->addAttribute($this->faker->word, $this->faker->text);
        $simpleXmlElement->addChild($this->faker->word, $this->faker->text);
        return $simpleXmlElement;
    }
}
