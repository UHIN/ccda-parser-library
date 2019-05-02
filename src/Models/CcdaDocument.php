<?php

namespace Uhin\Ccda\Models;

use Uhin\Ccda\Exceptions\IllegalOperation;
use Uhin\Ccda\Exceptions\InvalidSourceXmlData;

class CcdaDocument
{
    /**
     * Factory method for creating this object from a SimpleXMLElement object.
     *
     * @param \SimpleXMLElement $simpleXMLElement
     * @return CcdaDocument
     * @see CcdaDocument::validateSimpleXmlElement()
     */
    public static function getDocumentFromSimpleXmlElement(\SimpleXMLElement $simpleXMLElement): CcdaDocument
    {
        if (static::validateSimpleXmlElement($simpleXMLElement)) { // Validate Passed SimpleXMLElement Object
            return new static($simpleXMLElement);
        } // End of Validate Passed SimpleXMLElement Object
    }

    /**
     * Factory method for creating this object from a file path to an XML document.
     *
     * @param string $filepath
     * @return CcdaDocument
     * @throws InvalidSourceXmlData
     * @see CcdaDocument::getDocumentFromSimpleXmlElement()
     */
    public static function getDocumentFromFilepath(string $filepath): CcdaDocument
    {
        $simpleXmlElement = simplexml_load_file($filepath);
        if ($simpleXmlElement instanceof \SimpleXMLElement) { // Verify SimpleXMLElement Object Created
            return static::getDocumentFromSimpleXmlElement($simpleXmlElement);
        } else { // Middle of Verify SimpleXMLElement Object Created
            throw new InvalidSourceXmlData('Invalid source XML file: ' . $filepath);
        } // End of Verify SimpleXMLElement Object Created
    }

    /**
     * Factory method for creating this object from an XML string.
     *
     * @param string $xmlString
     * @return CcdaDocument
     * @throws InvalidSourceXmlData
     * @see CcdaDocument::getDocumentFromSimpleXmlElement()
     */
    public static function getDocumentFromXmlString(string $xmlString): CcdaDocument
    {
        $simpleXmlElement = simplexml_load_string($xmlString);
        if ($simpleXmlElement instanceof \SimpleXMLElement) { // Verify SimpleXMLElement Object Created
            return static::getDocumentFromSimpleXmlElement($simpleXmlElement);
        } else { // Middle of Verify SimpleXMLElement Object Created
            throw new InvalidSourceXmlData('Invalid source XML string: ' . $xmlString);
        } // End of Verify SimpleXMLElement Object Created
    }

    /**
     * Validates a passed SimpleXMLElement object conforms to the HL-7 v3 standard. Called from the factory method(s)
     * before instantiating an object through the constructor.
     *
     * @param \SimpleXMLElement $simpleXmlElement
     * @return bool
     * @see CcdaDocument::getDocumentFromSimpleXmlElement()
     */
    protected function validateSimpleXmlElement(\SimpleXMLElement $simpleXmlElement): bool
    {
        return true; // @todo Add CCDA XML Document Validation Here
    }

    /**
     * Contains the source C-CDA XML document passed to the constructor by the factory methods.
     *
     * @var \SimpleXMLElement
     * @see CcdaDocument::__construct()
     */
    protected $simpleXmlElement;

    /**
     * Contains the namespace array from \SimpleXMLElement::getNamespaces(true)
     *
     * @var array
     * @see CcdaDocument::__construct()
     */
    protected $namespaces;

    /**
     * Contains the C-CDA document in array (i.e. dictionary) format
     *
     * @var array
     * @see CcdaDocument::__construct()
     * @see CcdaDocument::parseElement()
     * @see CcdaDocument::toArray()
     */
    protected $data;

    /**
     * Contains the string that is prepended to element attribute names (to distinguish them from the element's value
     * and/or children in the resulting array).
     *
     * @var string
     * @see CcdaDocument::parseElementAttributesIntoNamespacedArray()
     */
    protected $elementAttributePrefix = 'attribute';

    /**
     * Contains the string that is used to delimit the element attribute prefix and the element attribute name.
     *
     * @var string
     * @see CcdaDocument::parseElementAttributesIntoNamespacedArray()
     */
    protected $elementAttributePrefixDelimiter = ':';

    /**
     * Constructor that is protected to force creation of this object to happen through factory methods with enforced
     * validation.
     *
     * CcdaDocument constructor.
     * @param \SimpleXMLElement $simpleXmlElement
     * @see CcdaDocument::getDocumentFromSimpleXmlElement()
     * @see CcdaDocument::parseElement()
     * @see CcdaDocument::$simpleXmlElement
     * @see CcdaDocument::$data
     */
    protected function __construct(\SimpleXMLElement $simpleXmlElement)
    {
        $this->simpleXmlElement = $simpleXmlElement;
        $this->namespaces = $this->simpleXmlElement->getNamespaces(true);
        $this->data = [$this->simpleXmlElement->getName() => $this->parseElement($this->simpleXmlElement)];
    }

    /**
     * Recursive method that translates a SimpleXMLElement object into an array (i.e. dictionary) format. The recursion
     * originates in a method called by this one (CcdaDocument::parseElementChildrenIntoNamespacedArray()) rather than by
     * this method directly.
     *
     * @param \SimpleXMLElement $simpleXmlElement
     * @return array
     * @see CcdaDocument::__construct()
     * @see CcdaDocument::parseElementAttributesIntoNamespacedArray()
     * @see CcdaDocument::parseElementChildrenIntoNamespacedArray()
     *
     */
    protected function parseElement(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        $value = trim((string) $simpleXmlElement);
        $attributes = $this->parseElementAttributesIntoNamespacedArray($simpleXmlElement);
        $children = $this->parseElementChildrenIntoNamespacedArray($simpleXmlElement);
        foreach (array_keys($this->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces to Merge Element Data
            if (empty($currentNamespacePrefix)) { // Check for Namespaced or Global

                // Global (i.e. Not Namespaced) Data
                if (!empty($attributes[$currentNamespacePrefix])) { // Check for Attributes in Namespace
                    $return = array_merge($return, $attributes[$currentNamespacePrefix]);
                } // End of Check for Attributes in Namespace
                if (!empty($children[$currentNamespacePrefix])) { // Check for Children in Namespace
                    $return = array_merge($return, $children[$currentNamespacePrefix]);
                } // End of Check for Children in Namespace

            } else { // Middle of Check for Namespaced or Global

                // Namespaced Data
                $return[$currentNamespacePrefix] = [];
                if (!empty($attributes[$currentNamespacePrefix])) { // Check for Attributes in Namespace
                    $return[$currentNamespacePrefix] = $attributes[$currentNamespacePrefix];
                } // End of Check for Attributes in Namespace
                if (!empty($children[$currentNamespacePrefix])) { // Check for Children in Namespace
                    $return[$currentNamespacePrefix] = array_merge($return[$currentNamespacePrefix], $children[$currentNamespacePrefix]);
                } // End of Check for Children in Namespace
                if (empty($return[$currentNamespacePrefix])) { // Purge Empty Namespace Containers from Return Value
                    unset($return[$currentNamespacePrefix]);
                } // End of Purge Empty Namespace Containers from Return Value

            } // End of Check for Namespaced or Global
        } // End of Loop through Namespaces to Merge Element Data
        if (!empty($value)) { // Check for Value
            $return['value'] = $value;
        } // End of Check for Value
        return $return;
    }

    /**
     * This method takes a SimpleXMLElement object and creates a namespace-aware array (i.e. dictionary) of the element's
     * attributes with them.
     *
     * @param \SimpleXMLElement $simpleXmlElement
     * @return array
     * @see CcdaDocument::parseElement()
     * @see CcdaDocument::$elementAttributePrefix
     * @see CcdaDocument::$elementAttributePrefixDelimiter
     */
    protected function parseElementAttributesIntoNamespacedArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        foreach (array_keys($this->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces
            $return[$currentNamespacePrefix] = [];
            foreach ($simpleXmlElement->attributes($currentNamespacePrefix, true) as $currentAttribute) { // Loop through Element Attributes in Current Namespace
                $return[$currentNamespacePrefix][$this->elementAttributePrefix . $this->elementAttributePrefixDelimiter . $currentAttribute->getName()] = (string) $currentAttribute;
            } // End of Loop through Element Attributes in Current Namespace
            if (empty($return[$currentNamespacePrefix])) { // Remove Namespaces that Have No Data/Attributes
                unset($return[$currentNamespacePrefix]);
            } // End of Remove Namespaces that Have No Data/Attributes
        } // End of Loop through Namespaces
        return $return;
    }

    /**
     * This method takes a SimpleXMLElement object and creates a namespace-aware array (i.e. dictionary) of the element's
     * children elements with them (this is where the CcdaDocument::parseElement() recursion comes from).
     *
     * @param \SimpleXMLElement $simpleXmlElement
     * @return array
     * @see CcdaDocument::parseElement()
     */
    protected function parseElementChildrenIntoNamespacedArray(\SimpleXMLElement $simpleXmlElement): array
    {
        $return = [];
        foreach (array_keys($this->namespaces) as $currentNamespacePrefix) { // Loop through Namespaces
            $return[$currentNamespacePrefix] = [];
            foreach ($simpleXmlElement->children($currentNamespacePrefix, true) as $currentChild) { // Loop through Element Children in Current Namespace
                if ($simpleXmlElement->{$currentChild->getName()}->count() > 1) { // Check for Repeating Child Groups (Multiple Children with Same Name or Array/List)
                    if (!is_array($return[$currentNamespacePrefix][$currentChild->getName()])) { // Make Sure Child Name Index Is An Array
                        $return[$currentNamespacePrefix][$currentChild->getName()] = [];
                    } // End of Make Sure Child Name Index Is An Array
                    $return[$currentNamespacePrefix][$currentChild->getName()][] = $this->parseElement($currentChild);
                } else { // Middle of Check for Repeating Child Groups (Multiple Children with Same Name or Array/List)
                    $return[$currentNamespacePrefix][$currentChild->getName()] = $this->parseElement($currentChild);
                } // End of Check for Repeating Child Groups (Multiple Children with Same Name or Array/List)
            } // End of Loop through Element Children in Current Namespace
            if (empty($return[$currentNamespacePrefix])) { // Remove Namespaces that Have No Data/Attributes
                unset($return[$currentNamespacePrefix]);
            } // End of Remove Namespaces that Have No Data/Attributes
        } // End of Loop through Namespaces
        return $return;
    }

    /**
     * Getter method for the array (i.e. dictionary) version of the SimpleXMLElement document used in the factory methods.
     *
     * @return array
     * @see CcdaDocument::$data
     * @see CcdaDocument::toStdClass()
     * @see CcdaDocument::toJson()
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Getter method for a \stdClass version of the array (i.e. dictionary) version of the SimpleXMLElement document
     * used in the factory methods.
     *
     * @return \stdClass
     * @see CcdaDocument::toArray()
     */
    public function toStdClass(): \stdClass
    {
        return (object) $this->toArray();
    }

    /**
     * Getter method for a JSON string version of the array (i.e. dictionary) version of the SimpleXMLElement document
     * used in the factory methods.
     *
     * @return string
     * @see CcdaDocument::toArray()
     * @see CcdaDocument::__toString()
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Getter method for a (JSON) string version of the array (i.e. dictionary) version of the SimpleXMLElement document
     * used in the factory methods.
     *
     * @return string
     * @see CcdaDocument::toJsonString()
     */
    public function __toString(): string
    {
        return $this->toJsonString();
    }

    /**
     * Magic getter for retrieving and interacting with the raw data attributes on this object.
     *
     * @param string $attributeName
     * @return mixed|null
     * @see CcdaDocument::$namespaces
     * @see CcdaDocument::$simpleXmlElement
     * @see CcdaDocument::$data
     * @see CcdaDocument::$elementAttributePrefix
     * @see CcdaDocument::$elementAttributePrefixDelimiter
     */
    public function __get(string $attributeName)
    {
        switch ($attributeName) { // Check Attribute Name Parameter

            case 'namespaces':
            case 'simpleXmlElement':
            case 'data':
            case 'elementAttributePrefix':
            case 'elementAttributePrefixDelimiter':
                $return = $this->{$attributeName};
                break;

            default:
                $return = null;
                break;

        } // End of Check Attribute Name Parameter
        return $return;
    }

    /**
     * Magic setter for changing some of the raw data attributes on this object. Most of the data attributes are not
     * allowed to be changed, though.
     *
     * @param string $attributeName
     * @param $attributeValue
     * @return string
     * @throws IllegalOperation
     */
    public function __set(string $attributeName, $attributeValue)
    {
        switch ($attributeName) { // Check Attribute Name Parameter

            case 'elementAttributePrefix':
            case 'elementAttributePrefixDelimiter':
                return $this->{$attributeName} = (string) $attributeValue;
                break;

            default:
                throw new IllegalOperation(sprintf('%s does not support setting this attribute: %s', get_called_class(), $attributeName));
                break;

        } // End of Check Attribute Name Parameter
    }
}
