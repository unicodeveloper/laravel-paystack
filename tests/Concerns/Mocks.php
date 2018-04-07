<?php 

namespace Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use Unicodeveloper\Paystack\Paystack;
use GuzzleHttp\Exception\RequestException;

trait Mocks 
{
	protected $client;

	function paystack ($response_type = null)
	{
		$this->mockedClient($response_type);

		return new Paystack($this->client);
	}

	function reflected($response_type = null, $class = null)
	{
		return is_null($class) ? (new Reflectors($this->paystack($response_type))) : (new Reflectors($class));
	}

	function mockedClient($response_type = null)
	{
		$handler = $this->handler($response_type);

		$this->client = new Client([
			"handler" => $handler,
		]);
	}

	function handler($response_type = null) 
	{
		return new MockHandler([
			$this->response($response_type ? $response_type : "payment_response"),
		]);
	}

	function response($response_type) 
	{
		$body = $this->getResource()[$response_type];

		return new Response(200, ["X-Foo" => "bar"], json_encode($body));
	}

	function getResource()
	{
		$resource = require __DIR__ . "/../Stubs/resource.php";

		return $resource;
	}
}