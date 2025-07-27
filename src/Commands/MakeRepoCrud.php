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
        $modelName = Str::studly($name);
        $variableName = Str::camel($name);
        $tableName = Str::snake(Str::pluralStudly($name));
        $routeName = Str::kebab(Str::studly($name));
        $viewFolder = $routeName;

        $this->info("Generating CRUD for: $name");

        $basePath = base_path('vendor\ahmed-hussain70\crud-generator\src\Stubs');

        if (file_exists($name)) {
            $this->warn("Name is already exist");
        }

        $context = compact('name', 'modelName', 'variableName', 'tableName', 'routeName', 'viewFolder');

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

        // Resources
        $this->addResource($basePath = 'vendor\ahmed-hussain70\crud-generator\src\Stubs\Repo_pattern', $name, $context);

        // Add Route
        $this->addAPIRoute($routeName, $modelName);

        // Add API Response
        $this->addApiResponse($basePath = 'vendor\ahmed-hussain70\crud-generator\src\Stubs\Repo_pattern');

        // Add Meta Controller
        $this->addMetaController($name);

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
            "$folderPath/{$name}Repository.php",
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
            "$folderPath/{$name}Service.php",
            $context
        );
    }

    // protected function addRequest($basePath, $name, $context)
    // {
    //     $folderPath = app_path('Http/Requests');

    //     if (!file_exists($folderPath)) {
    //         mkdir($folderPath, 0777, true);
    //     }

    //     $this->generateFile(
    //         "$basePath/requests.stub",
    //         "$folderPath/Store{$name}Request.php",
    //         $context
    //     );

    //     $this->generateFile(
    //         "$basePath/requests.stub",
    //         "$folderPath/Update{$name}Request.php",
    //         $context
    //     );
    // }

    protected function addRequest($basePath, $name, $context)
    {
        $folderPath = app_path('Http/Requests');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Add class_name to context for Store
        $storeContext = array_merge($context, [
            'class_name' => "Store{$name}Request"
        ]);
        $this->generateFile(
            "$basePath/requests.stub",
            "$folderPath/Store{$name}Request.php",
            $storeContext
        );

        // Add class_name to context for Update
        $updateContext = array_merge($context, [
            'class_name' => "Update{$name}Request"
        ]);
        $this->generateFile(
            "$basePath/requests.stub",
            "$folderPath/Update{$name}Request.php",
            $updateContext
        );
    }

    protected function addResource($basePath, $name, $context)
    {
        $folderPath = app_path('Http/Resources');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $this->generateFile(
            "$basePath/resource.stub",
            app_path("Http/Resources/{$name}Resource.php"),
            $context
        );
    }

    protected function addAPIRoute($routeName, $modelName)
    {
        $file = base_path('routes/api.php');

        $useStatement = "use App\Http\Controllers\\{$modelName}Controller;";

        $routeEntry = "
Route::prefix('$routeName')->group(function () {
    Route::get('/', [{$modelName}Controller::class, 'all']);
    Route::post('/', [{$modelName}Controller::class, 'store']);
    Route::put('/{id}', [{$modelName}Controller::class, 'update']);
    Route::delete('/{id}', [{$modelName}Controller::class, 'delete']);
});
        ";

        $fileContents = File::get($file);

        if (!Str::contains($fileContents, $useStatement)) {
            $fileContents = preg_replace(
                '/<\?php\s*/',
                "<?php\n\n$useStatement\n",
                $fileContents,
                1
            );
        }

        if (!Str::contains($fileContents, $routeEntry)) {
            $fileContents .= "\n" . $routeEntry;
            File::put($file, $fileContents);
            $this->info("Route and use statement added to api.php");
        } else {
            $this->warn("Route already exists in api.php");
        }
    }

    protected function addApiResponse($basePath)
    {
        $folderPath = app_path('Helpers');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $this->generateFile(
            "$basePath/apiResponse.stub",
            "$folderPath/ApiResponseHelper.php",
            []
        );
    }

    protected function addMetaController($name)
    {
        $filePath = app_path('Http/Controllers/MetaController.php');

        $useCreateRequest = "use App\\Http\\Requests\\Store{$name}Request;";
        $useUpdateRequest = "use App\\Http\\Requests\\Update{$name}Request;";
        $useModel = "use App\\Models\\{$name};";

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        // If file does not exist, create basic controller structure
        if (!file_exists($filePath)) {
            $template = <<<PHP
<?php

namespace App\Http\Controllers;

class MetaController extends Controller
{
    public static function metadataMap(): array
    {
        return [
        ];
    }
}

PHP;
            File::put($filePath, $template);
        }

        // Load file contents
        $fileContents = File::get($filePath);

        // Insert use statements after <?php if not already present
        $uses = [$useModel, $useCreateRequest, $useUpdateRequest];
        foreach ($uses as $use) {
            if (!Str::contains($fileContents, $use)) {
                $fileContents = preg_replace('/<\?php\s*/', "<?php\n{$use}\n", $fileContents, 1);
            }
        }

        // Add metadata line inside metadataMap() method
        $lineEntry = "            '{$name}' => {$name}::metadata(new Store{$name}Request(), new Update{$name}Request()),";

        if (!Str::contains($fileContents, $lineEntry)) {
            $fileContents = preg_replace(
                '/(return\s*\[\s*)([^]]*)/s',
                "\$1\n{$lineEntry},\n\$2",
                $fileContents
            );
            File::put($filePath, $fileContents);
            $this->info("Metadata entry and use statements added to MetaController.php");
        } else {
            $this->warn("Metadata entry already exists in MetaController.php");
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
            ['{{name}}', '{{modelName}}', '{{variableName}}', '{{tableName}}', '{{class_name}}'],
            [$replacements['name'] ?? '', $replacements['modelName'] ?? '', $replacements['variableName'] ?? '', $replacements['tableName'] ?? '', $replacements['class_name'] ?? ''],
            $stub
        );

        File::put($toPath, $stub);
        $this->info("Created: " . str_replace(base_path() . '/', '', $toPath));
    }
}
