<?php

namespace CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepoCrud extends Command
{
    protected $signature = 'make:repo-crud {name}';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $modelVariable = Str::camel($name);
        $tableName = Str::snake(Str::pluralStudly($name));
        $routeName = Str::kebab(Str::pluralStudly($name));
        $viewFolder = $routeName;

        $this->info("Generating CRUD for: $name");

        $basePath = base_path('vendor\ahmed-hussain70\crud-generator\src\Stubs');

        if(file_exists($name)){
            $this->warn("Name is already exist");
        }
        // Model
        $this->generateFile(
            "$basePath/model.stub",
            app_path("Models/{$name}.php"),
            compact('name', 'modelVariable', 'tableName')
        );

        // Controller
        $this->generateFile(
            "$basePath/Repo/controller.stub",
            app_path("Http/Controllers/{$name}Controller.php"),
            compact('name', 'modelVariable', 'tableName')
        );


        // Services
        $folderPath = app_path('Http/Services');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $this->generateFile(
            "$basePath/Repo/service.stub",
            "$folderPath/{$name}.php",
            compact('name', 'modelVariable', 'tableName')
        );

        // Requests
        $this->generateFile(
            "$basePath/requests.stub",
            app_path("Http/Requests/{$name}Request.php"),
            compact('name', 'modelVariable', 'tableName')
        );

        // Add Route
        $routeEntry = "Route::resource('$routeName', \\App\\Http\\Controllers\\{$name}Controller::class);";

        $file = base_path('routes/api.php');

        if(!Str::contains(File::get($file), $routeEntry)){
            File::append($file, "\n" . $routeEntry);
            $this->info("Route added to api.php");
        }else{
            $this->warn("Route is exist in api.php");
        }

        // Add Exceptions
        $this->generateFile(
            "$basePath/Exceptions/handler.stub",
            app_path("Exceptions/Handler.php"),
            ""
        );

        $this->generateFile(
            "$basePath/Exceptions/response.stub",
            app_path("Exceptions/Response.php"),
            ""
        );

        $this->info("CRUD for $name generated successfully!");
    }

    protected function generateFile($stubPath, $destinationPath, $replacements)
    {
        if (!File::exists($stubPath)) {
            $this->error("Stub not found: $stubPath");
            return;
        }

        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{modelName}}', '{{modelVariable}}', '{{tableName}}'],
            [$replacements['name'], $replacements['modelVariable'], $replacements['tableName']],
            $stub
        );

        File::put($destinationPath, $stub);
        $this->info("Created: " . str_replace(base_path() . '/', '', $destinationPath));
    }
}
