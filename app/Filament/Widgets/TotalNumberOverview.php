<?php

namespace App\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Services\SupaBase\AdminPanel\AdminPanel as AdminPanelService;

class TotalNumberOverview extends BaseWidget
{
    private $supaService;
    public function __construct()
    {
        $this->supaService = new AdminPanelService();
    }
    protected function getStats(): array
    {
        $statistics = $this->supaService->fetchStatistics();
        // dd($this->supaService->fetchChartData());
        return [
            Stat::make('Clients',$statistics['clients_count'])
            ->description('Registerd Today: '.$statistics['today_clients_count'])
            ->descriptionColor('primary')
            ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Drivers ',$statistics['drivers_count'])
                ->description('Registered Today: '.$statistics['today_drivers_count'])
                ->descriptionColor('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Pickup Requests ',$statistics['pickups_count'])
                ->description('Done Today: '.$statistics['today_pickups_count'])
                ->descriptionColor(Color::Rose)
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}
