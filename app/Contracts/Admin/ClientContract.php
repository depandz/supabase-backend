<?php

namespace App\Contracts\Admin;

use Illuminate\Http\File;
use Illuminate\Support\Collection;
use App\Contracts\SupaBaseContract;
use App\DataTransferObjects\PanelClientDTO as ClientObject;

interface ClientContract extends SupaBaseContract
{
    public function insert(array $data): ClientObject;

    public function update(string $s_id, array $data): ClientObject;

    public function suspend(string $s_id): void;

    public function activateAccount(string $s_id): void;

    public function delete(string $s_id): bool|null;
    
    public function restore(string $s_id): void;
}
