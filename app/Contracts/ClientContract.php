<?php

namespace App\Contracts;

use Illuminate\Http\File;
use App\DataTransferObjects\ClientDTO as ClientObject;

interface ClientContract extends SupaBaseContract
{
    public function insert(array $data): ClientObject;

    public function update(int $id, array $data): ClientObject;

    public function updatePhoto(int $id, File $file): string;
}
