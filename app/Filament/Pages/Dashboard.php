<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function mount(): void
    {
        session()->forget([
            'dashboard_start_date', 
            'dashboard_end_date'
        ]);
    }
}
