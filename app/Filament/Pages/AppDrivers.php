<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Contracts\Admin\DriverContract;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Services\SupaBase\AdminPanel\PanelDrivers;

class AppDrivers extends Page
{
    use InteractsWithTable;
    protected static ?string $navigationIcon ='icon-driver';
    public static bool $shouldRegisterNavigation =false;

    protected static string $view = 'filament.pages.app-drivers';

    private $service;
    public function __construct()
    {
        $this->service = new PanelDrivers();
    }

    protected function getViewData(): array
    {
       $drivers = $this->service->fetchAll();

       return [
        'drivers'=>$drivers
       ];
    }

}
