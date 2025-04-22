<?php

namespace CrudGenerator;

use CrudGenerator\Commands\MakeCrud;
use CrudGenerator\Commands\MakeRepoCrud;
use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCrud::class,
            ]);

            $this->commands([
                MakeRepoCrud::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}

