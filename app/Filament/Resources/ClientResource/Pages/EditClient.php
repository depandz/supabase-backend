<?php

namespace App\Filament\Resources\ClientResource\Pages;

use Filament\Actions;
use App\Models\Client;
use Filament\Actions\Action;
use App\Enums\AccountStatus;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ClientResource;
use App\Services\SupaBase\AdminPanel\PanelClients;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;
    protected $service;

    public function __construct()
    {
        $this->service = new PanelClients();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Action::make('Suspend')
            ->requiresConfirmation()
            ->icon('heroicon-o-no-symbol')
            ->color('warning')
            ->action(function(Client $client) {

                $result = (new PanelClients())->suspend($client->s_id);

                    Notification::make()
                        ->title('Client Suspended Successfully')
                        ->success()
                        ->send();
            })
            ->successNotification(null)
            ->visible(fn(Client $client) =>$client->account_status == AccountStatus::$ACTIVE ),
        Action::make('Activate Account')
            ->requiresConfirmation()
            ->icon('heroicon-o-check-circle')
            ->color('warning')
            ->action(function(Client $client) {

                $result = (new PanelClients())->activateAccount($client->s_id);

                    Notification::make()
                        ->title('Client Account Activated Successfully')
                        ->success()
                        ->send();
            })
            ->successNotification(null)
            ->visible(fn(Client $client) =>($client->account_status == AccountStatus::$SUSPENDED) || ($client->account_status == AccountStatus::$PENDING)  )
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $client = $this->service->update($this->record->s_id,$data);

        return [];
    }
}
