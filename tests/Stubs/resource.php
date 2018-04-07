<?php 

return [
	"payment_response" => [
		"data" => [
			"authorization_url" => "http://example.org",
			"access_code" => 123456
		]
	],

	"validation_response_success" => [
		"message" => "Verification successful",
	],

	"validation_response_invalid" => [
		"message" => "Invalid transaction reference",
	],

	"validation_response_other" => [
		"message" => "Unknown",
	],

	"all_customers" => json_decode(file_get_contents(__DIR__ . "/customers/all.json"), true),

	"created_customers" => json_decode(file_get_contents(__DIR__ . "/customers/create.json"), true),
	
	"fetch_customers" => json_decode(file_get_contents(__DIR__ . "/customers/fetch.json"), true),
	
	"update_customers" => json_decode(file_get_contents(__DIR__ . "/customers/update.json"), true),

	"all_plans" => json_decode(file_get_contents(__DIR__ . "/plans/all.json"), true),

	"created_plan" => json_decode(file_get_contents(__DIR__ . "/plans/create.json"), true),

	"all_transactions" => json_decode(file_get_contents(__DIR__ . "/sample_transactions.json"), true),
];