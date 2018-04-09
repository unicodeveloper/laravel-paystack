<?php 

return [
	/*----------------------------------------------------------------------------------
	 * Generic responses.															   |
	 *----------------------------------------------------------------------------------
	 */
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

	/*----------------------------------------------------------------------------------
	 * Customer responses.															   |
	 *----------------------------------------------------------------------------------
	 */
	"all_customers" => json_decode(file_get_contents(__DIR__ . "/customers/all.json"), true),

	"created_customers" => json_decode(file_get_contents(__DIR__ . "/customers/create.json"), true),
	
	"fetch_customers" => json_decode(file_get_contents(__DIR__ . "/customers/fetch.json"), true),
	
	"update_customers" => json_decode(file_get_contents(__DIR__ . "/customers/update.json"), true),

	/*----------------------------------------------------------------------------------
	 * Plans responses.															   	   |
	 *----------------------------------------------------------------------------------
	 */
	"all_plans" => json_decode(file_get_contents(__DIR__ . "/plans/all.json"), true),

	"created_plan" => json_decode(file_get_contents(__DIR__ . "/plans/create.json"), true),

	/*----------------------------------------------------------------------------------
	 * Transactions responses.														   |
	 *----------------------------------------------------------------------------------
	 */
	"all_transactions" => json_decode(file_get_contents(__DIR__ . "/sample_transactions.json"), true),

	"export_transactions" => [
		"status" => true,
		"message" => "Export successful",
		"data" => [
		"path" => "https://files.paystack.co/exports/100032/1460290758207.csv"
		]
	],

	/*----------------------------------------------------------------------------------
	 * Subscriptions responses.														   |
	 *----------------------------------------------------------------------------------
	 */
	"created_subscription" => json_decode(file_get_contents(__DIR__ . "/subscriptions/create.json"), true),

	"all_subscriptions" => json_decode(file_get_contents(__DIR__ . "/subscriptions/all.json"), true),

	"fetch_subscription" => json_decode(file_get_contents(__DIR__ . "/subscriptions/fetch.json"), true),

	"enabled_subscription" => [
		"status" => true,
		"message" => "Subscription enabled successfully"
	],

	"disabled_subscription" => [
		"status" => true,
		"message" => "Subscription disabled successfully"
	],

	/**
	 * ----------------------------------------------------------------------------------
	 * Pages responses.														   		    |
	 * ----------------------------------------------------------------------------------
	 */
	"created_page" => json_decode(file_get_contents(__DIR__ . "/pages/create.json"), true),

	"all_pages" => json_decode(file_get_contents(__DIR__ . "/pages/all.json"), true),

	"fetched_page" => json_decode(file_get_contents(__DIR__ . "/pages/fetch.json"), true),

	"updated_page" => json_decode(file_get_contents(__DIR__ . "/pages/update.json"), true),

	/**
	 * ----------------------------------------------------------------------------------
	 * Subaccount responses.												   		    |
	 * ----------------------------------------------------------------------------------
	 */
	"created_subaccount" => json_decode(file_get_contents(__DIR__ . "/subaccounts/create.json"), true),

	"fetched_subaccount" => json_decode(file_get_contents(__DIR__ . "/subaccounts/fetch.json"), true),

	"all_subaccount" => json_decode(file_get_contents(__DIR__ . "/subaccounts/all.json"), true),

	"updated_subaccount" => json_decode(file_get_contents(__DIR__ . "/subaccounts/update.json"), true),
];