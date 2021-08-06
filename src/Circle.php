<?php

namespace Keinher\Circle;

use Psr\Http\Client\ClientInterface;
use Keinher\Circle\Resources\AbstractResource;
use Shuttle\Shuttle;
use ReflectionClass;
use UnexpectedValueException;

/**
 * @property \Keinher\Circle\Resources\Payments $payments
 * @property \Keinher\Circle\Resources\Encryption $encryption
 * @property \Keinher\Circle\Resources\Payouts $payouts
 */
class Circle
{
	/**
	 * ClientInterface instance.
	 *
	 * @var ClientInterface|null
	 */
	protected $httpClient;

	/**
	 * Apy keys
	 *
	 * @var array
	 */
	protected $api_keys = [
		'payments' => '',
		'payouts' => ''
	];

	/**
	 * Circle API host environment.
	 *
	 * @var string
	 */
	protected $environment = "sandbox";

	/**
	 * Circle API environments and matching hostname.
	 *
	 * @var array<string,string>
	 */
	protected $circleEnvironments = [
		// "production" => "",
		"sandbox" => "https://api-sandbox.circle.com/v1/",
	];

	/**
	 * Resource instance cache.
	 *
	 * @var array<AbstractResource>
	 */
	protected $resource_cache = [];

	/**
	 * @param string $environment Possible values are: production, sandbox
	 * @param string $request_ip
	 */
	public function __construct(
		string $environment = "sandbox",
		array $api_keys
	) {
		if (!\array_key_exists($environment, $this->circleEnvironments)) {
			throw new UnexpectedValueException("Invalid environment. Environment must be one of: production, development, or sandbox.");
		}

		$this->environment = $environment;
		
		foreach ($api_keys as $key => $value) {
			if (!array_key_exists($key, $this->api_keys)) {
				throw new UnexpectedValueException("Invalid api_key index. Index must be: payments or payouts.");
			}
			$this->api_keys[$key] = $value;
		}
	}

	/**
	 * Magic getter for resources.
	 *
	 * @param string $resource
	 * @throws UnexpectedValueException
	 * @return AbstractResource
	 */
	public function __get(string $resource): AbstractResource
	{
		if (!isset($this->resource_cache[$resource])) {

			$resource = \str_replace([" "], "", \ucwords(\str_replace(["_"], " ", $resource)));

			$resource_class = "\\Keinher\\Circle\\Resources\\" . $resource;

			if (!\class_exists($resource_class)) {
				throw new UnexpectedValueException("Unknown Circle resource: {$resource}");
			}

			$reflectionClass = new ReflectionClass($resource_class);

			// Get the api key for especific resource
			$api_key = $this->api_keys[strtolower($resource)];

			if (empty($api_key)) {
				throw new UnexpectedValueException("Circle api key not found for: {$resource}");
			}

			/**
			 * @var AbstractResource $resource_instance
			 */
			$resource_instance = $reflectionClass->newInstanceArgs([
				$this->getHttpClient(),
				$api_key,
				$this->plaidEnvironments[$this->environment]
			]);

			$this->resource_cache[$resource] = $resource_instance;
		}

		return $this->resource_cache[$resource];
	}

	/**
	 * Set a specific ClientInterface instance to be used to make HTTP calls.
	 *
	 * @param ClientInterface $httpClient
	 * @return void
	 */
	public function setHttpClient(ClientInterface $httpClient): void
	{
		$this->httpClient = $httpClient;
	}

	/**
	 * Get the ClientInterface instance being used to make HTTP calls.
	 *
	 * @return ClientInterface
	 */
	public function getHttpClient(): ClientInterface
	{
		if (empty($this->httpClient)) {
			$this->httpClient = new Shuttle;
		}

		return $this->httpClient;
	}
}
