<?php

namespace Keinher\Circle\Entities;

class Billing
{
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * City
     *
     * @var string
     */
    protected $city;

    /**
     * Country
     *
     * @var string
     */
    protected $country;

    /**
     * Line1
     *
     * @var string
     */
    protected $line1;

    /**
     * Line2
     *
     * @var string|null
     */
    protected $line2;

    /**
     * District
     *
     * @var string|null
     */
    protected $district;

    /**
     * Postal code
     *
     * @var string
     */
    protected $postal_code;

    public function __construct(
        string $name,
        string $city,
        string $country,
        string $line1,
        ?string $line2 = null,
        ?string $district = null,
        string $postal_code
    ) {
        $this->name = $name;
        $this->city = $city;
        $this->country = $country;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->district = $district;
        $this->postal_code = $postal_code;
    }

    public function toArray(): array
    {
        return \array_filter(
            [
                "name" => $this->name,
                "city" => $this->city,
                "country" => $this->country,
                "line1" => $this->line1,
                "line2" => $this->line2,
                "district" => $this->district,
                "postalCode" => $this->postal_code,
            ],
            function ($value): bool {
                return $value !== null;
            }
        );
    }
}
