<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;
use Str;

class CreateQualifyingFormatCommand extends Command
{
    protected $signature = 'format:make {name}';
    protected $description = 'Creates scaffolding for a new qualifying format';

    public function __construct(private readonly Filesystem $filesystem)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $stubsContent = $this->getStubsContent();

        if ($this->createModel($stubsContent['model'])) {
            $this->info("Model \"{$this->getModelFileName()}\" created");
        }

        if ($this->createMigration($stubsContent['migration'])) {
            $this->info("Migration \"{$this->getMigrationFileName()}\" created");
        }

        if ($this->createFactory($stubsContent['factory'])) {
            $this->info("Factory \"{$this->getFactoryFileName()}\" created");
        }

        return 0;
    }

    private function getStubsContent(): array
    {
        $path = $this->getStubDirectory();

        $stubs = [
            'migration' => file_get_contents($path.'/migration.stub'),
            'model' => file_get_contents($path.'/model.stub'),
            'factory' => file_get_contents($path.'/factory.stub'),
        ];

        foreach ($stubs as $key => $stub) {
            $stubs[$key] = $this->replaceStubsContent($stub);
        }

        return $stubs;
    }

    private function getStubDirectory(): string
    {
        return base_path('stubs/QualifyingFormats');
    }

    private function replaceStubsContent(string $stub): string
    {
        $variables = $this->getStubVariables();
        return str_replace(array_keys($variables), array_values($variables), $stub);
    }

    private function getStubVariables(): array
    {
        return [
            '$CLASSNAME$' => $this->getClassName(),
            '$TABLENAME$' => $this->getTableName(),
        ];
    }

    private function getClassName(): string
    {
        return ucfirst(Pluralizer::singular($this->argument('name')));
    }

    private function getTableName(): string
    {
        return Str::snake(Pluralizer::plural($this->argument('name')));
    }

    private function getModelFileName(): string
    {
        return "{$this->getClassName()}.php";
    }

    private function getMigrationFileName(): string
    {
        return "create_{$this->getTableName()}_table.php";
    }

    private function getFactoryFileName(): string
    {
        return "{$this->getClassName()}Factory.php";
    }

    private function createModel(string $modelContent): bool
    {
        $modelPath = app_path('Models/QualifyingFormats');
        $filePath = "$modelPath/{$this->getModelFileName()}";

        if ($this->filesystem->exists($filePath)) {
            $this->warn("A model with the name \"{$this->getModelFileName()}\" already exists");
            return false;
        }

        return $this->filesystem->put($filePath, $modelContent);
    }

    private function createMigration(string $migrationContent): bool
    {
        $migrationPath = base_path('database/migrations');
        $filePath = "$migrationPath/{$this->getMigrationFileName()}";

        if ($this->filesystem->exists($filePath)) {
            $this->warn("A migration with the name \"{$this->getMigrationFileName()}\" already exists");
            return false;
        }

        return $this->filesystem->put($filePath, $migrationContent);
    }

    private function createFactory(string $factoryContent): bool
    {
        $factoryPath = base_path('database/factories/QualifyingFormats');
        $filePath = "$factoryPath/{$this->getFactoryFileName()}";

        if ($this->filesystem->exists($filePath)) {
            $this->warn("A factory with the name \"{$this->getFactoryFileName()}\" already exists");
            return false;
        }

        return $this->filesystem->put($filePath, $factoryContent);
    }
}
