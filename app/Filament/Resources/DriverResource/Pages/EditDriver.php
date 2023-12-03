<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DriverResource;
use App\Services\SupaBase\Adminpanel\PanelDrivers;

class EditDriver extends EditRecord
{
    protected static string $resource = DriverResource::class;
    protected $service;

    public function __construct()
    {
        $this->service = new PanelDrivers();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $driver = $this->service->update($this->record->s_id,$data);

        return [];
    }
    
}
