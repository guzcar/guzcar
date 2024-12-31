<?php

namespace App\Services;

use App\Livewire\FilamentBreezy\BrowserSessions;
use \Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;
use Jeffgreco13\FilamentBreezy\Livewire\SanctumTokens;
use Jeffgreco13\FilamentBreezy\Livewire\TwoFactorAuthentication;
use Jeffgreco13\FilamentBreezy\Livewire\UpdatePassword;
use Livewire\Livewire;

class CustomBreezyCore extends BreezyCore
{

    protected $browserSessions = false;

    public function boot(Panel $panel): void
    {
        if ($this->myProfile) {
            if ($this->sanctumTokens) {
                Livewire::component('sanctum_tokens', SanctumTokens::class);
                $this->myProfileComponents([
                    'sanctum_tokens' => SanctumTokens::class,
                ]);
            }
            if ($this->twoFactorAuthentication) {
                Livewire::component('two_factor_authentication', TwoFactorAuthentication::class);
                $this->myProfileComponents([
                    'two_factor_authentication' => TwoFactorAuthentication::class,
                ]);
            }
            if ($this->browserSessions) {
                Livewire::component('browser_sessions', BrowserSessions::class);
                $this->myProfileComponents([
                    'browser_sessions' => BrowserSessions::class,
                ]);
            }

            Livewire::component('personal_info', PersonalInfo::class);
            Livewire::component('update_password', UpdatePassword::class);
            $this->myProfileComponents([
                'personal_info' => PersonalInfo::class,
                'update_password' => UpdatePassword::class,
            ]);
            if ($this->myProfile['shouldRegisterUserMenu']) {
                if ($panel->hasTenancy()) {
                    $tenantId = request()->route()->parameter('tenant');
                    if ($tenantId && $tenant = app($panel->getTenantModel())::where($panel->getTenantSlugAttribute() ?? 'id', $tenantId)->first()) {
                        $panel->userMenuItems([
                            'account' => MenuItem::make()->url($this->getMyProfilePageClass()::getUrl(panel: $panel->getId(), tenant: $tenant)),
                        ]);
                    }
                } else {
                    $panel->userMenuItems([
                        'account' => MenuItem::make()->url($this->getMyProfilePageClass()::getUrl()),
                    ]);
                }
            }
        }
    }

    public function enableBrowserSessions(bool $condition = true)
    {
        $this->browserSessions = $condition;

        return $this;
    }
}
