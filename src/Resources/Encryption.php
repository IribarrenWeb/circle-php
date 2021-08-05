<?php

namespace Keinher\Circle\Resources;

class Encryption extends AbstractResource
{
    /**
     * Get a public key for encyption
     *
     */
    public function getKey(): object {
        return $this->sendRequest(
            "post",
            "encryption/public"
        );
    }
}
