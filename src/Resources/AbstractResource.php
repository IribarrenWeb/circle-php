<?php

namespace Keinher\Circle\Resources;

use Capsule\Request;
use Keinher\Circle\Exceptions\CircleRequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;
use Ramsey\Uuid\Uuid;

abstract class AbstractResource
{
	/**
	 * ClientInterface instance.
	 *
	 * @var ClientInterface
	 */
	protected $httpClient;

	/**
	 * Idempotency key
	 *
	 * @var string
	 */
	protected $idempotency_key;

	/**
	 * Plaid client Id.
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Resource api key
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Circle hostname to use.
	 *
	 * @var string
	 */
	private $hostname;

	/**
	 * @param ClientInterface $httpClient
	 * @param string $api_key
	 * @param string $hostname
	 */
	public function __construct(
		ClientInterface $httpClient,
		string $api_key,
		string $hostname
	) {
		$this->httpClient = $httpClient;
		$this->api_key = $api_key;
		$this->idempotency_key = $this->getIdempotencyKey();
		$this->hostname = $hostname;
	}

	/**
	 * Build request body with client credentials.
	 *
	 * @param array<array-key,mixed> $params
	 * @return array
	 */
	protected function paramsWithClientCredentials(array $params = []): array
	{
		return \array_merge([
			"idempotency_key" => $this->idempotency_key,
		], $params);
	}

	/**
	 * Send a request and parse the response.
	 *
	 * @param string $method
	 * @param string $path
	 * @param array<array-key,mixed> $params
	 * @return object
	 */
	protected function sendRequest(string $method, string $path, array $params = []): object
	{
		$response = $this->sendRequestRawResponse($method, $path, $params, $this->api_key);

		$payload = \json_decode($response->getBody()->getContents());

		if (\json_last_error() !== JSON_ERROR_NONE) {
			throw new UnexpectedValueException("Invalid JSON response returned by Circle");
		}

		return (object) $payload;
	}

	/**
	 * Make an HTTP request and get back the ResponseInterface instance.
	 *
	 * @param string $method
	 * @param string $path
	 * @param array<array-key,mixed> $params
	 * @return ResponseInterface
	 */
	protected function sendRequestRawResponse(string $method, string $path, array $params = []): ResponseInterface
	{
		$response = $this->httpClient->sendRequest(
			$this->buildRequest($method, $path, $params, $this->api_key)
		);

		if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
			throw new CircleRequestException($response);
		}

		return $response;
	}

	/**
	 * Build the RequestInterface instance to be sent by the HttpClientInterface instance.
	 *
	 * @param string $method
	 * @param string $path
	 * @param array<array-key,mixed> $params
	 * @return RequestInterface
	 */
	protected function buildRequest(string $method, string $path, array $params = []): RequestInterface
	{
		return new Request(
			$method,
			$this->hostname . \trim($path, "/"),
			\json_encode((object) $params),
			[
				"Authorization" => "Bearer {$this->api_key}",
				"Content-Type" => "application/json"
			]
		);
	}

	/**
	 * Undocumented function
	 *
	 * @return string
	 */
	public function getIdempotencyKey() : string
	{
		return Uuid::uuid1()->toString();
	}

	/**
	 * Undocumented function
	 *
	 * @return string
	 */
	public function getApiKey() : string
	{
		return $this->api_key;
	}
}
