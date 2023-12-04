<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\AccountStatus;
use Filament\Resources\Resource;
use App\Tables\Columns\ViewPhoto;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Columns\AccountStatusColumn;
use App\Filament\Resources\ClientResource\Pages;
use App\Services\SupaBase\Adminpanel\PanelClients;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClientResource\RelationManagers;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'icon-clients';

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
                Forms\Components\TextInput::make('email')
                    ->maxLength(255),
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
                ViewPhoto::make('photo'),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('reported_count')->searchable()->sortable(),
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Client deleted')
                    ->before(function (Client $client) {
                        $result = (new PanelClients())->delete($client->s_id);

                        if (!$result) {
                            Notification::make()
                                ->title('There is an error occured when deleting client')
                                ->icon('heroicon-o-x-circle')
                                ->iconColor('danger')
                                ->color('danger')
                                ->persistent()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Client Deleted Successfully')
                                ->success()
                                ->send();
                        }
                    })
                    ->successNotification(null),
                Tables\Actions\RestoreAction::make()
                ->requiresConfirmation()
                    ->color('warning')
                    ->action(function(Client $client) {

                        $result = (new PanelClients())->restore($client->s_id);

                            Notification::make()
                                ->title('Client Account Restored Successfully')
                                ->success()
                                ->send();
                    }),
                Tables\Actions\Action::make('Suspend')
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
                Tables\Actions\Action::make('Activate Account')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-no-symbol')
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
            'view' => Pages\ViewClient::route('/{record}'),
        ];
    }    
}
