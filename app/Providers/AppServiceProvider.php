<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Reservations\ReservationRepositoryInterface;
use App\Repositories\Reservations\ReservationRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
