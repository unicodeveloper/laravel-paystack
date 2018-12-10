<?php

namespace Unicodeveloper\Paystack;


class Endpoint
{
	const INIT_TRANSACTION = "/transaction/initialize";
	const VERIFY_TRANSACTION = "/transaction/verify/";
	const EXPORT_TRANSACTION = "/transaction/export";
	const TRANSACTIONS = "/transaction";
	const PLANS = "/plan";
	const CREATE_PLAN = "/plan";
	const FETCH_PLAN = "/plan/";
	const UPDATE_PLAN = "/plan/";
	const CUSTOMERS = "/customer";
	const CREATE_CUSTOMER = "/customer";
	const FETCH_CUSTOMER = "/customer/";
	const UPDATE_CUSTOMER = "/customer/";
	const SUBSCRIPTIONS = "/subscription";
	const CREATE_SUBSCRIPTION = "/subscription";
	const GET_CUSTOMER_SUBSCRIPTION = "/subscription?customer=";
	const GET_PLAN_SUBSCRIPTION = "/subscription?plan=";
	const ENABLE_SUBSCRIPTION = "/subscription/enable";
	const DISABLE_SUBSCRIPTION = "/subscription/disable";
	const FETCH_SUBSCRIPTION = "/subscription/";
	const CREATE_PAGE = "/page";
	const GET_ALL_PAGES = "/page";
	const FETCH_PAGE = "/page";
	const UPDATE_PAGE = "/page/";
	const CREATE_SUB_ACCOUNT = "/subaccount";
	const FETCH_SUB_ACCOUNT = "/subaccount/";
	const LIST_SUB_ACCOUNT = "/subaccount/?perPage=";
	const UPDATE_SUB_ACCOUNT = "/subaccount/?perPage=";
}