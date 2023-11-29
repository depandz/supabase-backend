<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\VehicleTypes;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DriverResource\Pages;
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
                    ->options(Province::pluck('name','id')),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('commercial_register_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('vehicle_type')
                    ->required()
                    ->options(VehicleTypes::toArray())
                    ->default(VehicleTypes::LIGHT->value),
                Forms\Components\TextInput::make('capacity')
                    ->required()
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
                ImageColumn::make('photo'),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('reported_count')->searchable()->sortable(),
                TextColumn::make('account_status')->searchable()->sortable(),
                TextColumn::make('vehicle_type')->searchable()->sortable(),
                TextColumn::make('commercial_register_number')->searchable()->sortable(),
                TextColumn::make('capacity')->searchable()->sortable(),
                TextColumn::make('can_transport_goods'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        ];
    }    
}
