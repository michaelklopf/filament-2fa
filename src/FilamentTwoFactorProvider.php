<?php

namespace Webbingbrasil\FilamentTwoFactor;

use Livewire\Livewire;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Spatie\LaravelPackageTools\Package;
use Webbingbrasil\FilamentTwoFactor\Pages\TwoFactor;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Webbingbrasil\FilamentTwoFactor\Http\Livewire\Auth;
use Webbingbrasil\FilamentTwoFactor\Http\Livewire\TwoFactorAuthenticationForm;

class FilamentTwoFactorProvider extends PackageServiceProvider
{
    public static string $name = 'filament-2fa';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageConfigured(Package $package): void
    {
        $package->hasRoute('web')
            ->hasMigration('add_two_factor_columns_to_users_table');

        Livewire::component(Auth\Login::getName(), Auth\Login::class);
        Livewire::component(Auth\TwoFactorChallenge::getName(), Auth\TwoFactorChallenge::class);
        Livewire::component('filament-two-factor-form', TwoFactorAuthenticationForm::class);
    }

    public function packageBooted(): void
    {
        if (config('filament-2fa.enable_two_factor_page')
            && config('filament-2fa.show_two_factor_page_in_user_menu')) {
            Filament::serving(function () {
                Filament::registerUserMenuItems([
                    UserMenuItem::make()
                        ->label(__('filament-2fa::two-factor.navigation_label'))
                        ->url(TwoFactor::getUrl())
                        ->icon('heroicon-s-lock-closed'),
                ]);
            });
        }
    }

    protected function getPages(): array
    {
        return config('filament-2fa.enable_two_factor_page')
            ? [TwoFactor::class]
            : [];
    }
}
