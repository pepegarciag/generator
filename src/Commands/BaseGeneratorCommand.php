<?php

namespace Kodeloper\Generator\Commands;

use Illuminate\Console\Command;

class BaseGeneratorCommand extends Command
{
    /**
     * The package config.
     *
     * @var CommandData
     */
    public $config;

    /**
     * @var Composer
     */
    public $composer;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = $this->getPackageConfig();
        $this->composer = app()['composer'];
    }

    /**
     * Get the package configuration.
     *
     * @return string
     */
    protected function getPackageConfig()
    {
        return config('generator');
    }
}
