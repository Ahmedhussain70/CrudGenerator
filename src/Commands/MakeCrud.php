<?php

namespace CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name}';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $modelVariable = Str::camel($name);
        $tableName = Str::snake(Str::pluralStudly($name));
        $routeName = Str::kebab(Str::pluralStudly($name));
        $viewFolder = $routeName;

        $this->info("Generating CRUD for: $name");

        $basePath = base_path('vendor\CrudGenerator\src\Stubs');

        // Model
        $this->generateFile(
            "$basePath/model.stub",
            app_path("Models/{$name}.php"),
            compact('name', 'modelVariable', 'tableName')
        );

        // Controller
        $this->generateFile(
            "$basePath/controller.stub",
            app_path("Http/Controllers/{$name}Controller.php"),
            compact('name', 'modelVariable', 'tableName')
        );

        // Views
        // $viewPath = resource_path("views/{$viewFolder}");
        // if (!File::exists($viewPath)) {
        //     File::makeDirectory($viewPath, 0755, true);
        // }

        // $views = ['index', 'create', 'edit', 'show'];
        // foreach ($views as $view) {
        //     $this->generateFile(
        //         "$basePath/view/{$view}.stub",
        //         "$viewPath/{$view}.blade.php",
        //         compact('name', 'modelVariable', 'tableName', 'routeName', 'viewFolder')
        //     );
        // }

        // Requests
        $this->generateFile(
            "$basePath/requests.stub",
            app_path("Http/Requests/{$name}Request.php"),
            compact('name', 'modelVariable', 'tableName')
        );

        // Add route
        $routeEntry = "Route::resource('$routeName', \\App\\Http\\Controllers\\{$name}Controller::class);";
        File::append(base_path('routes/api.php'), "\n" . $routeEntry);
        $this->info("Route added to api.php");

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
