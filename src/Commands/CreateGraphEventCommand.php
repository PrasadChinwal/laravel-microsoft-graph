<?php

namespace PrasadChinwal\MicrosoftGraph\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Input\InputArgument;

class CreateGraphEventCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:graph-event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Microsoft Graph Event';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Microsoft Graph Event Class';

    /**
     * @return array|string|string[]
     */
    protected function getNameInput(): array|string
    {
        return str_replace('.', '/', trim($this->argument('name')));
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name): string
    {
        $replace = [];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__.'/../stubs/CalendarEvent.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Mail';
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the model class.'],
        ];
    }
}
