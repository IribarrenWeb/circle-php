<?php

namespace Keinher\Circle\Resources;

use Keinher\Circle\Entities\Billing;
use Keinher\Circle\Entities\User;

class Payments extends AbstractResource
{
	/**
	 * Create a payment
	 *
	 * @param array|string $source
	 * @param float $amount
	 * @param string $currency
	 * @param string $verification
	 * @param string $description
	 * @param array|null $data (encyptedData, verificationSuccessUrl, verificationFailureUrl)
	 * @return object
	 */
	public function create(
		array $source,
		User $userMetadata,
		float $amount,
		string $currency = 'USD',
		string $verification = 'none',
		string $description = 'Payment for Ebitmarket',
		?array $data = null
	): object {

		$params = [
			"verification" => $verification,
			"amount" => [
				"amount" => $amount,
				"currency" => $currency
			],
			"source" => $source,
			"description" => $description,
			"metadata" => $userMetadata->toArray()
		];

		if (!empty($data)) {
			$params = array_merge($data, $params);
		}

		return $this->sendRequest(
			"post",
			"payments",
			$this->paramsWithClientCredentials($params),
		);
	}

	/**
	 * Get payment details.
	 *
	 * @param string $payment_id
	 * @return object
	 */
	public function get(string $payment_id): object
	{
		$params = [
			"payment_id" => $payment_id
		];

		return $this->sendRequest(
			"get",
			"payments/" . $payment_id,
		);
	}

	/**
	 * Cancel the payment.
	 *
	 * @param array $options
	 * @return object
	 */
	public function cancel(string $payment_id): object
	{
		return $this->sendRequest(
			"post",
			"payments/" . $payment_id . "/cancel",
			$this->paramsWithClientCredentials(),
		);
	}

	public function createCard(
		Billing $billing,
		string $encryptedData,
		string $keyId,
		User $userMetadata,
		string $expMonth,
		string $expYear
	) {
		$params = [
			"keyId" => $keyId,
			"encryptedData" => $encryptedData,
			"billingDetails" => $billing->toArray(),
			"expMonth" => $expMonth,
			"expYear" => $expYear,
			"metadata" => $userMetadata->toArray()
		];

		return $this->sendRequest(
			"post",
			"cards",
			$this->paramsWithClientCredentials($params),
		);
	}

	/**
	 * Create ach bank account
	 *
	 * @param string $plaidToken
	 * @param Billing $billing
	 * @param User $userMetadata
	 * @return void
	 */
	public function createAchBank(
		string $plaidToken,
		Billing $billing,
		User $userMetadata
	) {
		$params = [
			"plaidProcessorToken" => $plaidToken,
			"billingDetails" => $billing->toArray(),
			"metadata" => $userMetadata->toArray()
		];

		return $this->sendRequest(
			"post",
			"banks/ach",
			$this->paramsWithClientCredentials($params),
		);
	}

	/**
	 * Get Ach bank details.
	 *
	 * @param string $bank_id
	 * @return object
	 */
	public function getAchBank(string $bank_id): object
	{
		return $this->sendRequest(
			"get",
			"banks/ach/" . $bank_id,
		);
	}

	/**
	 * Get card details.
	 *
	 * @param string $card_id
	 * @return object
	 */
	public function getCard(string $card_id): object
	{
		return $this->sendRequest(
			"get",
			"cards/" . $card_id,
		);
	}

	/**
	 * Get balance from circle account
	 *
	 * @return object
	 */
	public function getFunds(): object
	{
		return $this->sendRequest(
			"get",
			"balances"
		);
	}
}
