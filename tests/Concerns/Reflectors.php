<?php 

namespace Tests\Concerns;

class Reflectors extends \Reflectionobject 
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
	public function invokeMethod ($methodName, array $args = [])
	{
		$method = parent::getMethod($methodName);

		$method->setAccessible(true);

		return $method->invokeArgs($this->object, $args);
	}

	public function fetchProperty(string $propertyName)
	{
		$property = parent::getProperty($propertyName);

		$property->setAccessible(true);

		return (object) [
			"name" => $property->getName(),
			"value" => $property->getValue($this->object)
		];
	}
}