<?php

namespace App\Services\Locale;

use Exception;
use App\Contracts\ClientContract;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use App\DataTransferObjects\ClientDTO as CLientObject;
use App\Enums\GlobalVars;
use App\Models\Client as ModelsClient;

class Client
{
    /**
     * find by otp an and phone
     * 
     * @return collection
     */
    public function findByPhone($phone_number)
    {
        $client = ModelsClient::whereDeletedAt(null)
            ->where('phone_number', $phone_number)
            ->firstOrFail();

        return $client;

    }
}