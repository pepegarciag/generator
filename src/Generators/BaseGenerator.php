<?php

namespace Kodeloper\Generator\Generators;

use Kodeloper\Generator\Utils\FileSystemUtil;
use Symfony\Component\Yaml\Yaml;

class BaseGenerator
{
    protected $stub;
    protected $schema;
    protected $config;
    protected $data;

    public function generateFile($path, $file)
    {
        $this->rollbackOldFile($path, $file);
        FileSystemUtil::createFile($path, $file, $this->stub);

        return $this;
    }

    public function rollbackOldFile($path, $file)
    {
        if (file_exists($path.$file)) {
            if (!copy($path.$file, $path.now()->format('d_m_Y_h_i_s').'_'.$file.'._bkp')) {
                return false;
            } else {
                return FileSystemUtil::deleteFile($path, $file);
            }
        }

        return false;
    }

    public function getSchema()
    {
        $schema_file = config('generator.schema.path') ?? 'base_schema.yml';

        return Yaml::parse(file_get_contents(base_path($schema_file)));
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

    /**
     * Replace template namespace with class namespace.
     *
     * @return $this
     */
    public function replaceNameSpace()
    {
        $this->stub = $stub = str_replace('{{Namespace}}', $this->config['namespace'], $this->stub);

        return $this;
    }

    /**
     * Replace template class name with correct class name.
     *
     * @return $this
     */
    public function replaceClassName()
    {
        $this->stub = $stub = str_replace('{{ClassName}}', $this->data['class_name'], $this->stub);

        return $this;
    }
}
