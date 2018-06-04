<?php

namespace Kodeloper\Generator\Commands;

use Kodeloper\Generator\Generators\ModelGenerator;

class ModelGeneratorCommand extends BaseGeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:model
                            {name : The name of the model.}
                            {--table= : The name of the table.}
                            {--pk= : The name of the primary key.}
                            {--soft-deletes= : Include soft deletes fields.}
                            {--from_schema= : Load configuration from schema file.} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new  model command';

    private $generator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->generator = new ModelGenerator();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $command_data = [];
        $this->line('Generating new model');
        $command_data['model'] = ucfirst($this->argument('name'));
        $command_data['table'] = $this->option('table') ?? str_plural($this->argument('name'));
        if ($this->option('from_schema')) {
            $this->line($this->generator->fromSchema($command_data));
        } else {
            $command_data['primary_key'] = $this->option('pk') ?? config('generator.models.primary_key');
            $softDeletes = $this->option('soft-deletes') ?? config('generator.models.soft_delete');
        }
    }
}
