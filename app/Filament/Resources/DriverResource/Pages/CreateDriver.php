<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use App\Filament\Resources\DriverResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SupaBase\Adminpanel\PanelDrivers;

class CreateDriver extends CreateRecord
{
    protected static string $resource = DriverResource::class;
    protected $service;

    public function __construct()
    {
        $this->service = new PanelDrivers();
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $driver = $this->service->insert($data);
        if($driver){
            Notification::make()
            ->title('Driver Created successfully')
            ->success()
            ->send();
        }
        return [];
    }
}
