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
        $modelName = Str::camel($name);
        $tableName = Str::snake(Str::pluralStudly($name));
        $routeName = Str::kebab(Str::pluralStudly($name));
        $viewFolder = $routeName;

        $this->info("Generating CRUD for: $name");

        $basePath = base_path('vendor\ahmed-hussain70\crud-generator\src\Stubs');

        if(file_exists($name)){
            $this->warn("Name is already exist");
        }

        $context = compact('name', 'modelName', 'tableName', 'routeName', 'viewFolder');

        // Model
        $this->addModel($basePath, $name, $context);

        // Controller
        $this->addController($basePath, $name, $context);

        // Repositories
        $this->addRepository($basePath, $name, $context);

        // Services
        $this->addService($basePath, $name, $context);

        // Requests
        $this->addRequest($basePath, $name, $context);

        // Add Route
        $this->addAPIRoute($routeName);

        // Add Exceptions
        // $this->generateFile(
        //     "$basePath/Exceptions/handler.stub",
        //     app_path("Exceptions/Handler.php"),
        //     ""
        // );

        // $this->generateFile(
        //     "$basePath/Exceptions/response.stub",
        //     app_path("Exceptions/Response.php"),
        //     ""
        // );

        $this->info("CRUD for $name generated successfully!");
    }

    protected function addModel($basePath, $name, $context)
    {
        $this->generateFile(
            "$basePath/model.stub",
            app_path("Models/{$name}.php"),
            $context
        );
    }

    protected function addController($basePath, $name, $context)
    {
        $this->generateFile(
            "$basePath/Repo_pattern/controller.stub",
            app_path("Http/Controllers/{$name}Controller.php"),
            $context
        );
    }

    protected function addRepository($basePath, $name, $context)
    {
        $folderPath = app_path('Http/Repositories');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $this->generateFile(
            "$basePath/Repo_pattern/Repository.stub",
            "$folderPath/{$name}.php",
            $context
        );
    }

    protected function addService($basePath, $name, $context)
    {
        $folderPath = app_path('Http/Services');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $this->generateFile(
            "$basePath/Repo_pattern/service.stub",
            "$folderPath/{$name}.php",
            $context
        );
    }

    protected function addRequest($basePath, $name, $context)
    {
        $this->generateFile(
            "$basePath/requests.stub",
            app_path("Http/Requests/{$name}Request.php"),
            $context
        );
    }

    protected function addAPIRoute($routeName)
    {
        $file = base_path('routes/api.php');
        $routeEntry = "Route::resource('$routeName', \App\Http\Controllers\{$routeName}Controller::class);";

        if(!Str::contains(File::get($file), $routeEntry)){
            File::append($file, "\n" . $routeEntry);
            $this->info("Route added to api.php");
        }else{
            $this->warn("Route is exist in api.php");
        }
    }

    protected function generateFile($stubPath, $toPath, $replacements)
    {
        if (!File::exists($stubPath)) {
            $this->error("Stub not found: $stubPath");
            return;
        }

        $stub = File::get($stubPath);

        $stub = str_replace(
            ['{{modelName}}', '{{modelName}}', '{{tableName}}'],
            [$replacements['name'], $replacements['modelName'], $replacements['tableName']],
            $stub
        );

        File::put($toPath, $stub);
        $this->info("Created: " . str_replace(base_path() . '/', '', $toPath));
    }
}
