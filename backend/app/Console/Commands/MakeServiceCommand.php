<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');

        $filesystem = new Filesystem();

        $name = str_replace('\\', '/', $name);

        $servicePath = app_path("Services/{$name}.php");

        if ($filesystem->exists($servicePath)) {
            $this->error('Service already exists!');
            return;
        }

        $filesystem->ensureDirectoryExists(dirname($servicePath));

        $namespace = $this->getNamespace($name);
        $className = class_basename($name);

        $stub = $this->getStub($namespace, $className);

        $filesystem->put($servicePath, $stub);

        $this->info("Service created successfully at {$servicePath}");
    }

    protected function getNamespace($name)
    {
        $baseNamespace = 'App\Services';

        if (str_contains($name, '/')) {
            $subNamespace = str_replace('/', '\\', dirname($name));
            return $baseNamespace . '\\' . $subNamespace;
        }

        return $baseNamespace;
    }

    protected function getStub($namespace, $className)
    {
        return <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    public function __construct()
    {
        //
    }
}

PHP;
    }
}