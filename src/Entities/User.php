<?php

namespace Keinher\Circle\Entities;

class User
{
	/**
	 * User ID
	 *
	 * @var string
	 */
	protected $id;

	/**
     * User email address
	 *
     * @var string
	 */
    protected $email_address;
    
    /**
     * User email address
	 *
     * @var string
	 */
    protected $ip_address;
    
    /**
     * User phone number
	 *
     * @var string|null
	 */
    protected $phone_number;

	public function __construct(
        string $id,
		string $email_address,
		string $ip_address,
		?string $phone_number = null
	)
	{
		$this->id = md5($id);
		$this->email_address = $email_address;
		$this->ip_address = $ip_address;
		$this->phone_number = $phone_number;
	}

	public function toArray(): array
	{
		return \array_filter(
			[
				"sessionId" => $this->id,
				"email" => $this->email_address,
				"ipAddress" => $this->ip_address,
				"phoneNumber" => $this->phone_number,
			],
			function($value): bool {
				return $value !== null;
			}
		);
	}
}