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
    const MAX_GUARDED = 2;
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
             ->replacePrimaryKey()
             ->replaceAttributes();

        // $this->generateFile($this->config['path'], $this->data['model'] . '.php');
        return $this->stub;
    }

    private function replaceSoftDelete()
    {
        $soft_delete = isset($this->schema['soft_delete']) ? $this->schema['soft_delete'] : $this->config['soft_delete'];

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

    private function getAttributes() {
        return collect($this->schema['attributes']);
    }

    private function getFillableAttributes() {
        return collect($this->schema['attributes'])->filter(function($value, $key) {
            return $this->AttributeIsFillable($value, $key);
        });
    }

    private function getGuardedAttributes() {
        return collect($this->schema['attributes'])->except($this->getFillableAttributes()->keys());
    }

    private function AttributeIsFillable($attribute , $key) {
        if (isset($attribute['guarded']) && $attribute['guarded'] == true) {
            return false;
        }
        if (isset($attribute['fillable']) && $attribute['fillable'] == false) {
            return false;
        }
        return true;
    }

    private function useGuarded() {
        return $this->getGuardedAttributes()->count() <= self::MAX_GUARDED;
    }

    private function replaceAttributes()
    {
        if ($this->useGuarded()) {
            $this->replaceGuardedAttributes();
        } else {
            $this->replaceFillableAttributes();
        }
    }

    private function replaceFillableAttributes()
    {
        $fields = $this->getFillableAttributes()->keys();
        $fillable = <<<EOT
    
    /**
     * Fillable attributes that can be mass-assignable.
     *
     * @var array   
     */
    protected \$fillable = $fields;
EOT;

        $this->stub = str_replace('{{guardedFields}}', $fillable, $this->stub);
        return $this;
    }

    private function replaceGuardedAttributes()
    {
        $fields = $this->getGuardedAttributes()->keys();
        $fillable = <<<EOT
    
    /**
     * Guarded attributes that can`t be mass-assignable.
     *
     * @var array   
     */
    protected \$guarded = $fields;
EOT;

        $this->stub = str_replace('{{guardedFields}}', $fillable, $this->stub);
        return $this;
    }
}