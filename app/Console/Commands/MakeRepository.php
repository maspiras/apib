<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name} {--model=}';
    protected $description = 'Create a repository, interface, and auto-bind it in AppServiceProvider';

    public function handle()
    {
        $name = $this->argument('name');
        $model = $this->option('model') ?? $name;

        $interfacePath = app_path("Repositories/{$name}RepositoryInterface.php");
        $classPath = app_path("Repositories/{$name}Repository.php");

        // Ensure Repositories folder exists
        if (!File::exists(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'));
        }

        // Auto-create model if it doesn't exist
    if (!class_exists("App\\Models\\{$model}")) {
        $this->call('make:model', ['name' => "{$model}"]);
        $this->info("📦 Model {$model} created.");
    }

        // Generate Interface
        $interfaceContent = "<?php

namespace App\Repositories;

interface {$name}RepositoryInterface
{
    public function all();
    public function find(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}
";
        File::put($interfacePath, $interfaceContent);

        // Generate Repository Class
        $classContent = "<?php

namespace App\Repositories;

use App\Models\\{$model};

class {$name}Repository implements {$name}RepositoryInterface
{
    protected \$model;

    public function __construct({$model} \$model)
    {
        \$this->model = \$model;
    }

    public function all()
    {
        return \$this->model->all();
    }

    public function find(\$id)
    {
        return \$this->model->findOrFail(\$id);
    }

    public function create(array \$data)
    {
        return \$this->model->create(\$data);
    }

    public function update(\$id, array \$data)
    {
        \$record = \$this->find(\$id);
        \$record->update(\$data);
        return \$record;
    }

    public function delete(\$id)
    {
        \$record = \$this->find(\$id);
        return \$record->delete();
    }
}
";
        File::put($classPath, $classContent);

        
    }
}