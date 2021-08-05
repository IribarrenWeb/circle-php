<?php

namespace Keinher\Circle\Resources;

class Payouts extends AbstractResource
{
    protected $idempotency_key;

    /**
     * Create a payout
     *
     * @param string $type
     * @param float $amount
     * @param string $destination_id
     * @param string $beneficiary_email
     * @param string $currency
     * @param array|null $source
     * @return object
     */
    public function create(
        string $type = 'ach',
        float $amount,
        string $destination_id,
        string $beneficiary_email,
        string $currency = 'USD',
        ?array $source = null
    ): object {

        $params = [
            "destination" => [
                "type" => $type,
                "id" => $destination_id
            ],
            "amount" => [
                "amount" => $amount,
                "currency" => $currency
            ],
            "metadata" => [
                $beneficiary_email
            ]
        ];

        if ($source) {
            $params['source'] = $source;
        }

        return $this->sendRequest(
            "post",
            "payouts",
            $this->paramsWithClientCredentials($params),
        );
    }

    /**
     * Get payout details.
     *
     * @param string $payout_id
     * @return object
     */
    public function get(string $payout_id): object
    {
        return $this->sendRequest(
            "get",
            "payouts/" . $payout_id,
        );
    }
}
