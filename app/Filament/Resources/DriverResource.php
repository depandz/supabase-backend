<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\VehicleTypes;
use App\Enums\AccountStatus;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use App\Tables\Columns\ViewPhoto;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\AccountStatusColumn;
use App\Filament\Resources\DriverResource\Pages;
use App\Services\SupaBase\Adminpanel\PanelDrivers;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DriverResource\RelationManagers;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'icon-driver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->required()
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->default('male'),
                Forms\Components\TextInput::make('identity_card_number')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('licence_plate')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('province_id')->label('Province')
                    ->required()
                    ->options(Province::pluck('name', 'code')),
                Forms\Components\TextInput::make('email')
                    ->maxLength(255),
                Forms\Components\TextInput::make('commercial_register_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('vehicle_type')
                    ->required()
                    ->options(VehicleTypes::toArray())
                    ->default(VehicleTypes::LIGHT->value),
                Forms\Components\TextInput::make('capacity')
                    ->numeric(),
                Forms\Components\Toggle::make('can_transport_goods'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable(),
                TextColumn::make('s_id')->searchable()->sortable(),
                TextColumn::make('first_name')->searchable()->sortable(),
                TextColumn::make('last_name')->searchable()->sortable(),
                TextColumn::make('phone_number')->searchable()->sortable(),
                TextColumn::make('gender')->searchable()->sortable(),
                TextColumn::make('identity_card_number')->searchable()->sortable(),
                TextColumn::make('licence_plate')->searchable()->sortable(),
                ViewPhoto::make('photo'),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('reported_count')->searchable()->sortable(),
                TextColumn::make('vehicle_type')->searchable()->sortable(),
                TextColumn::make('commercial_register_number')->searchable()->sortable(),
                TextColumn::make('capacity')->searchable()->sortable(),
                TextColumn::make('can_transport_goods'),
                AccountStatusColumn::make('account_status'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('account_status')
                    ->options([
                        'pending' => 'Pending',
                        'suspended' => 'Suspended',
                        'active' => 'Active',
                    ]),
                SelectFilter::make('province_id')->label('Province')
                    ->options(Province::pluck('name', 'code'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
                Tables\Actions\RestoreAction::make()
                ->requiresConfirmation()
                    ->color('warning')
                    ->action(function(Driver $driver) {

                        $result = (new PanelDrivers())->restore($driver->s_id);

                            Notification::make()
                                ->title('Driver Account Restored Successfully')
                                ->success()
                                ->send();
                    }),
                Tables\Actions\Action::make('Suspend')
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
                Tables\Actions\Action::make('Activate Account')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-no-symbol')
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
            'view' => Pages\ViewDriver::route('/{record}'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
