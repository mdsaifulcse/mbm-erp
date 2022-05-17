<?php

namespace App\Providers;

use App\Contracts\Hr\AttendanceProcessInterface;
use App\Contracts\Hr\BillAnnounceInterface;
use App\Contracts\Hr\BonusInterface;
use App\Contracts\Hr\EmployeeInterface;
use App\Contracts\Hr\SalaryInterface;
use App\Contracts\Merch\PoBomInterface;
use App\Repository\Hr\AttendanceProcessRepository;
use App\Repository\Hr\BillAnnounceRepository;
use App\Repository\Hr\BonusRepository;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\SalaryRepository;
use App\Repository\Merch\PoBomRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() 
    { 
        $this->app->bind(PoBomInterface::class, PoBomRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);
        $this->app->bind(SalaryInterface::class, SalaryRepository::class);
        $this->app->bind(BonusInterface::class, BonusRepository::class);
        $this->app->bind(AttendanceProcessInterface::class, AttendanceProcessRepository::class);
        $this->app->bind(BillAnnounceInterface::class, BillAnnounceRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
