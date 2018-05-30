<?php

namespace Kodeloper\Generator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class GeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shows the generator package information';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Package created using Bootpack.');
        //$yamlContents = Yaml::parse(file_get_contents(__DIR__ . '/../base_schema.yml'));
        $yamlContents = Yaml::parse(file_get_contents(base_path('base_schema.yml')));
        var_dump($yamlContents);
    }
}
