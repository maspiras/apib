<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Reservations\ReservationRepositoryInterface;
use App\Repositories\Reservations\ReservationRepository;

use App\Repositories\Reservations\ReservedRoomRepositoryInterface;
use App\Repositories\Reservations\ReservedRoomRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(ReservedRoomRepositoryInterface::class, ReservedRoomRepository::class);

        $this->app->bind(\App\Services\Contracts\ReservationServiceInterface::class, \App\Services\ReservationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
