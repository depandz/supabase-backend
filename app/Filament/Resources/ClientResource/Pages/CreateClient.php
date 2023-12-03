<?php

namespace App\Filament\Resources\ClientResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SupaBase\Adminpanel\PanelClients;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;
    protected $service;

    public function __construct()
    {
        $this->service = new PanelClients();
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $client = $this->service->insert($data);
        if($client){
            Notification::make()
            ->title('Client Created successfully')
            ->success()
            ->send();
        }
        return [];
    }
}
