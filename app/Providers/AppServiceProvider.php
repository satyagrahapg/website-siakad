<?php

namespace App\Providers;

use App\Models\JamPelajaran;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Mapel;
use App\Observers\JamPelajaranObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    
    public function boot()
    {       
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Logika baru untuk role aktif
        Blade::if('role', function ($role) {
            $activeRole = session('active_role');
            $rolesArray = is_array($role) ? $role: explode('|', $role);
            return in_array($activeRole, $rolesArray);
        });
        
        // Share the mapel list only if the user has the 'Guru' role
        View::composer('*', function ($view) {
            if (Auth::check() && Auth::user()->hasRole('Guru')) {
                $listMapels = Mapel::select('nama', 'kelas')->distinct()->get();
                $view->with('listMapels', $listMapels);
            }
        });

        JamPelajaran::observe(JamPelajaranObserver::class);
    }
}
