<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Actions;
use App\Models\Driver;
use App\Enums\AccountStatus;
use Tables\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Actions\ActionGroup;
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
            Actions\ViewAction::make(),
            ActionGroup::make([
                Actions\DeleteAction::make()
                ->successNotificationTitle('Driver deleted')
                ->before(function (Driver $driver) {
                    $result = (new PanelDrivers())->delete($driver->s_id);

                    if (!$result) {
                        Notification::make()
                            ->title('There is an error occured when deleting driver')
                            ->icon('heroicon-o-x-circle')
                            ->iconColor('danger')
                            ->color('danger')
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Driver Deleted Successfully')
                            ->success()
                            ->send();
                    }
                })
                ->successNotification(null),
            Actions\RestoreAction::make()
            ->requiresConfirmation()
                ->color('warning')
                ->action(function(Driver $driver) {

                    $result = (new PanelDrivers())->restore($driver->s_id);

                        Notification::make()
                            ->title('Driver Account Restored Successfully')
                            ->success()
                            ->send();
                }),
            Actions\Action::make('Suspend')
                ->requiresConfirmation()
                ->icon('heroicon-o-no-symbol')
                ->color('warning')
                ->action(function(Driver $driver) {

                    $result = (new PanelDrivers())->suspend($driver->s_id);

                        Notification::make()
                            ->title('Driver Suspended Successfully')
                            ->success()
                            ->send();
                })
                ->successNotification(null)
                ->visible(fn(Driver $driver) =>$driver->account_status == AccountStatus::$ACTIVE ),
            Actions\Action::make('Activate Account')
                ->requiresConfirmation()
                ->icon('heroicon-o-check-circle')
                ->color('warning')
                ->action(function(Driver $driver) {

                    $result = (new PanelDrivers())->activateAccount($driver->s_id);

                        Notification::make()
                            ->title('Driver Account Activated Successfully')
                            ->success()
                            ->send();
                })
                ->successNotification(null)
                ->visible(fn(Driver $driver) =>($driver->account_status == AccountStatus::$SUSPENDED) || ($driver->account_status == AccountStatus::$PENDING)  )
            ])
            ->link()
            ->label('Actions')
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {

        $driver = $this->service->update($this->record->s_id,$data);

        return [];
    }
    
}
