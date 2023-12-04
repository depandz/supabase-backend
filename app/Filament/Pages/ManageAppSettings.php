<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use App\Settings\AppSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AppSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('app_name')
                ->required(),
                FileUpload::make('app_logo')
                ->image()
                ->directory('settings')
                ->preserveFilenames()
                ->required(),
                TextInput::make('app_description')
                ->required(),
                TextInput::make('contact_mail')
                ->required(),
                TextInput::make('customer_service_number')
                ->required(),
                TextInput::make('whatsapp_number')
                ->required(),
                TextInput::make('facebook_link')
                ->required(),
                TextInput::make('twitter_link')
                ->required(),
                TextInput::make('website_link')
                ->required(),
                TextInput::make('youtube_link')
                ->required(),
               
            ]);
    }
}
