<?php 

namespace Tests\Concerns;

class Reflectors extends \ReflectionClass
{
	public $object;

	public function __construct($args)
	{
		$this->object = $args;

		parent::__construct($args);
	}

	/**
	 * Call protected method.
	 * 
	 * @param  string $methodName
	 * @param  array  $args       
	 * @return mixed
	 */
	public function invokeMethod ($methodName, $args = [])
	{
		$method = parent::getMethod($methodName);

		$method->setAccessible(true);

		return $method->invokeArgs($this->object, $args);
	}

	/**
	 * Get details of a protected property from a class
	 * 
	 * @param  string $propertyName 
	 * @return stdClass=
	 */
	public function fetchProperty($propertyName)
	{
		$property = $this->getProperty($propertyName);

		return (object) [
			"name" => $property->getName(),
			"value" => $property->getValue($this->object)
		];
	}

	public function setProperty($propertyName, $value)
	{
		$property = $this->getProperty($propertyName);

		$property->setValue($this->object, $value);
	}

	/**
	 * Get property while setting it accessible.
	 * 
	 * @param  string $propertyName 
	 * @return mixed
	 */
	public function getProperty($propertyName)
	{
		$property = parent::getProperty($propertyName);

		$property->setAccessible(true);

		return $property;
	}
}