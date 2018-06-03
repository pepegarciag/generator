<?php
/**
 * Created by PhpStorm.
 * User: leon
 * Date: 3/6/18
 * Time: 18:13
 */

namespace Kodeloper\Generator\Generators;


class ModelGenerator extends BaseGenerator
{
    protected $dates;

    /**
     * Get the model stub file path for the generator.
     *
     * @return string
     */
    protected function getStubFilePath()
    {
        return config('generator.custom_stubs')
            ? config('generator.custom_stubs.path') . '/Model.stub'
            : __DIR__ . '/../Stubs/Model.stub';
    }

    public function fromSchema(Array $data) {
        $this->stub = file_get_contents($this->getStubFilePath());
        $this->schema = $this->getSchema()[$data['model']];
        $this->config = $this->getPackageConfig()['models'];

        $this->data = $data;
        $this->data['class_name'] = $this->data['model'];

        $this->replaceNameSpace()
             ->replaceModelToExtend()
             ->replaceClassName()
             ->replaceSoftDelete()
             ->replaceTableName()
             ->replacePrimaryKey();

        $this->generateFile($this->config['path'], $this->data['model'] . '.php');

        return $this->stub;
    }

    private function replaceSoftDelete()
    {
        $soft_delete = isset($this->schema['soft_delete']) ? $this->schema['soft_delete'] : $this->config['soft_delete'];
        $soft_delete = true;

        if ($soft_delete) {
            $this->stub = str_replace('{{useSoftDeletes}}', 'use Illuminate\Database\Eloquent\SoftDeletes;', $this->stub);
            $this->stub = str_replace('{{softDeletes}}', "use SoftDeletes;\n\t", $this->stub);
        } else {
            $this->stub = str_replace(['{{useSoftDeletes}}','{{softDeletes}}'], '' ,$this->stub);
        }

        return $this;
    }

    private function replaceTableName() {
        $this->stub = $stub = str_replace('{{table}}', $this->data['table'], $this->stub);
        return $this;
    }

    private function replacePrimaryKey() {
        $primaryKey =  $this->schema['primary_key'] ?? $this->config['primary_key'];
        $this->stub = $stub = str_replace('{{primaryKey}}', $primaryKey , $this->stub);
        return $this;
    }

    private function replaceModelToExtend() {
        $classToExtend = $this->schema['extends'] ?? $this->config['extends'];
        $this->stub = $stub = str_replace('{{modelToExtend}}', $classToExtend , $this->stub);
        return $this;
    }
}