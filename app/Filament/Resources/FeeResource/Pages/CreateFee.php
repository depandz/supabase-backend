<?php

namespace App\Filament\Resources\FeeResource\Pages;

use Filament\Actions;
use App\Filament\Resources\FeeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Services\SupaBase\AdminPanel\PanelProvincesFees;

class CreateFee extends CreateRecord
{
    protected static string $resource = FeeResource::class;
    protected $service;

    public function __construct()
    {
        $this->service = new PanelProvincesFees();
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $driver = $this->service->insert($data);
        if($driver){
            Notification::make()
            ->title('Province Fee Created successfully')
            ->success()
            ->send();
        }
        return [];
    }
}
