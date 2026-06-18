<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name} {--crud}';
    protected $description = 'Create a new service and its interface (with optional CRUD methods)';

    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = "{$name}Service";
        $interfaceName = "{$serviceName}Interface";

        // Paths
        $interfacePath = app_path("Services/Contracts/{$interfaceName}.php");
        $servicePath   = app_path("Services/{$serviceName}.php");

        // Ensure directories exist
        File::ensureDirectoryExists(app_path('Services/Contracts'));
        File::ensureDirectoryExists(app_path('Services'));

        // Create Interface
        if (!File::exists($interfacePath)) {
            $content = $this->option('crud')
                ? $this->generateCrudInterface($interfaceName, $name)
                : $this->generateInterface($interfaceName);

            File::put($interfacePath, $content);
            $this->info("Created: {$interfacePath}");
        } else {
            $this->warn("Interface already exists: {$interfacePath}");
        }

        // Create Service
        if (!File::exists($servicePath)) {
            $content = $this->option('crud')
                ? $this->generateCrudService($serviceName, $interfaceName, $name)
                : $this->generateService($serviceName, $interfaceName);

            File::put($servicePath, $content);
            $this->info("Created: {$servicePath}");
        } else {
            $this->warn("Service already exists: {$servicePath}");
        }

        // Auto-bind
        $this->bindInServiceProvider($interfaceName, $serviceName);
    }

    private function generateInterface($interfaceName)
    {
        return <<<PHP
<?php

namespace App\Services\Contracts;

interface {$interfaceName}
{
    public function example();
}
PHP;
    }

    private function generateService($serviceName, $interfaceName)
    {
        return <<<PHP
<?php

namespace App\Services;

use App\Services\Contracts\\{$interfaceName};

class {$serviceName} implements {$interfaceName}
{
    public function example()
    {
        // TODO: Implement logic
    }
}
PHP;
    }

    private function generateCrudInterface($interfaceName, $model)
    {
        return <<<PHP
<?php

namespace App\Services\Contracts;

use App\Models\\{$model};

interface {$interfaceName}
{
    public function getAll();
    public function getById(int \$id): ?{$model};
    public function create(array \$data): {$model};
    public function update({$model} \$model, array \$data): {$model};
    public function delete({$model} \$model): bool;
}
PHP;
    }

    private function generateCrudService($serviceName, $interfaceName, $model)
    {
        return <<<PHP
<?php

namespace App\Services;

use App\Models\\{$model};
use App\Services\Contracts\\{$interfaceName};

class {$serviceName} implements {$interfaceName}
{
    public function getAll()
    {
        return {$model}::all();
    }

    public function getById(int \$id): ?{$model}
    {
        return {$model}::find(\$id);
    }

    public function create(array \$data): {$model}
    {
        return {$model}::create(\$data);
    }

    public function update({$model} \$model, array \$data): {$model}
    {
        \$model->update(\$data);
        return \$model;
    }

    public function delete({$model} \$model): bool
    {
        return \$model->delete();
    }
}
PHP;
    }

    private function bindInServiceProvider($interfaceName, $serviceName)
    {
        $providerPath = app_path('Providers/AppServiceProvider.php');
        $content = File::get($providerPath);

        $binding = "\\App\\Services\\Contracts\\{$interfaceName}::class,\n            \\App\\Services\\{$serviceName}::class";

        if (!str_contains($content, $binding)) {
            $pattern = '/public function register\(\)\n    \{\n/';
            $replacement = "public function register()\n    {\n        \$this->app->bind(\n            {$binding}\n        );\n\n";
            $newContent = preg_replace($pattern, $replacement, $content);
            File::put($providerPath, $newContent);

            $this->info("Auto-bound {$interfaceName} to {$serviceName} in AppServiceProvider.");
        } else {
            $this->warn("Binding already exists in AppServiceProvider.");
        }
    }
}