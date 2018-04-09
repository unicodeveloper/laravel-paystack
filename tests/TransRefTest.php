<?php 

namespace Tests;

use Tests\Concerns\Reflectors;
use Unicodeveloper\Paystack\TransRef;

class TransRefTest extends TestCase 
{
	const ENCODING = [
		["alnum", "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",],
		["alpha", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",],
		["hexdec", "0123456789abcdef",],
		["numeric", "0123456789",],
		["nozero", "123456789",],
		["distinct", "2345679ACDEFHJKLMNPRSTUVWXYZ",],
		["other", "other",],
	];
	const POOL_ALNUM = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const POOL_ALPHA = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	const POOL_HEXDEC = "0123456789abcdef";

	/** 
	 * @test
	 * @dataProvider provider
	 */
	public function it_gets_pools($key, $value)
	{
		$reflection = new Reflectors(new TransRef);

		$pool = $reflection->invokeMethod("getPool", [$key]);

		$this->assertEquals($value, $value);
	}

	/**
	 * Data provider for all forms of encoding.
	 * 
	 * @return array
	 */
	public function provider ()
	{
		return self::ENCODING;
	}

	/**
	 * @test
	 * @@doesNotPerformAssertions
	 */
	public function it_resets_min ()
	{
		$reflection = new Reflectors(new TransRef);

		$reflection->invokeMethod("secureCrypt", [5, 0]);
	}
}