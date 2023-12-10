<?php

namespace App\Filament\Resources;

use App\Models\Fee;
use Filament\Forms;
use Filament\Tables;
use App\Models\Province;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeeResource\RelationManagers;

class FeeResource extends Resource
{
    protected static ?string $model = Fee::class;

    protected static ?string $navigationIcon = 'icon-fees';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->options(Province::orderBy('code','asc')->pluck('name','code'))
                    ->searchable()
                    ->default(fn (?Fee $record): string => Province::whereCode($record?->province_id)->first()?->name ?? ''),
                Forms\Components\TextInput::make('heavy')->label('Heavy Price')
                    ->numeric(),
                Forms\Components\TextInput::make('light')->label('Light Price')
                    ->numeric(),
                Forms\Components\TextInput::make('truck')->label('Truck Price')
                    ->numeric(),
                Forms\Components\TextInput::make('full_percentage')->hint('When it is full, add a percentage to the price')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province_id')
                    ->numeric()
                    ->sortable()
                    ->tooltip(fn (Fee $record): string => Province::whereCode($record->province_id)->first()?->name ?? ''),
                Tables\Columns\TextColumn::make('heavy')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('light')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('truck')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_percentage')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->successNotification(null),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListFees::route('/'),
            'create' => Pages\CreateFee::route('/create'),
            'edit' => Pages\EditFee::route('/{record}/edit'),
        ];
    }
}
