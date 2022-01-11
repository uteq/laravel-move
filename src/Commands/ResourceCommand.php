<?php

namespace Uteq\Move\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Uteq\Move\Finders\ModelFinder;

class ResourceCommand extends GeneratorCommand
{
    public $signature = 'move:resource {name} {--model=}';

    public $description = 'Form a new resource class';

    protected $type = 'Resource';

    protected function buildClass($name)
    {
        if (! $model = $this->option('model')) {
            $this->ask('What is the name of the model you are using? You can always change this afterwards');
        }

        $model = app(ModelFinder::class)
            ->find(is_null($model) ? $this->argument('name') : $model);

        return str_replace('{{ namespacedModel }}', $model, parent::buildClass($name));
    }

    protected function getStub()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'stubs/resource.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Move';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model class being represented.'],
        ];
    }
}
