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

	function paystack ()
	{
		$this->mockedClient();

		die((string)$this->client->request("get", '/go')->getBody());
		return new Paystack($this->client);
	}

	function reflected($class = null)
	{
		return is_null($class) ? (new Reflectors($this->paystack())) : (new Reflectors($class));
	}

	function mockedClient()
	{
		$handler = $this->handler();

		$this->client = new Client([
			"handler" => $handler,
		]);
	}

	function handler() 
	{
		$body = require __DIR__ . "/../Stubs/resource.php";

		return new MockHandler([
			new Response(200, ["X-Foo" => "bar"], json_encode($body)),
		]);
	}
}