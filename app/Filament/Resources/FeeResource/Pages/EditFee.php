<?php

namespace App\Filament\Resources\FeeResource\Pages;

use Filament\Actions;
use App\Filament\Resources\FeeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Services\SupaBase\AdminPanel\PanelProvincesFees;

class EditFee extends EditRecord
{
    protected static string $resource = FeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected $service;

    public function __construct()
    {
        $this->service = new PanelProvincesFees();
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $fee = $this->service->update($this->record->id,$data);

        if($fee){
            Notification::make()
            ->title('Province Fee Updated successfully')
            ->success()
            ->send();
        }
        return [];
    }
}
