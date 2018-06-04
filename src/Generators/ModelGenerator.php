<?php

namespace Kodeloper\Generator\Generators;

class ModelGenerator extends BaseGenerator
{
    const MAX_GUARDED = 2;
    protected $dates;
    protected $relations = [];
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

    public function fromSchema(array $data)
    {
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
             ->replaceAttributes()
             ->replaceDates()
             ->replaceRelationShips();

        $this->generateFile($this->config['path'], $this->data['model'] . '.php');
        return $this->stub;
    }

    private function replaceSoftDelete()
    {
        $soft_delete = isset($this->schema['soft_delete']) ? $this->schema['soft_delete'] : $this->config['soft_delete'];

        if ($soft_delete) {
            $soft_delete_field = isset($this->schema['soft_delete']['field']) ?
                $this->schema['soft_delete']['field'] : $this->config['timestamps']['fields']['deleted_at'];
            $this->stub = str_replace('{{useSoftDeletes}}', 'use Illuminate\Database\Eloquent\SoftDeletes;', $this->stub);
            $this->stub = str_replace('{{softDeletes}}', "use SoftDeletes;\n\t", $this->stub);
            $this->dates = [$soft_delete_field => $soft_delete_field];
        } else {
            $this->stub = str_replace(['{{useSoftDeletes}}','{{softDeletes}}'], '', $this->stub);
        }

        return $this;
    }

    private function replaceTableName()
    {
        $this->stub = $stub = str_replace('{{table}}', $this->data['table'], $this->stub);
        return $this;
    }

    private function replacePrimaryKey()
    {
        $primaryKey =  $this->schema['primary_key'] ?? $this->config['primary_key'];
        $this->stub = $stub = str_replace('{{primaryKey}}', $primaryKey, $this->stub);
        return $this;
    }

    private function replaceModelToExtend()
    {
        $classToExtend = $this->schema['extends'] ?? $this->config['extends'];
        $this->stub = $stub = str_replace('{{modelToExtend}}', $classToExtend, $this->stub);
        return $this;
    }

    private function getAttributes()
    {
        return collect($this->schema['attributes']);
    }

    private function getFillableAttributes()
    {
        return collect($this->schema['attributes'])
            ->filter(function ($value, $key) {
                return $this->AttributeIsFillable($value, $key);
            });
    }

    private function getDateAttributes()
    {
        return collect($this->schema['attributes'])
            ->whereIn('type', ['date','dateTime','dateTimeTz','time','timeTz','timestamp','timestampTz','year']);
    }

    private function getGuardedAttributes()
    {
        return collect($this->schema['attributes'])
            ->except($this->getFillableAttributes()
            ->keys());
    }

    private function AttributeIsFillable($attribute, $key)
    {
        if (isset($attribute['guarded']) && $attribute['guarded'] == true) {
            return false;
        }
        if (isset($attribute['fillable']) && $attribute['fillable'] == false) {
            return false;
        }
        return true;
    }

    private function useGuarded()
    {
        return $this->getGuardedAttributes()->count() <= self::MAX_GUARDED;
    }

    private function replaceAttributes()
    {
        if ($this->useGuarded()) {
            return $this->replaceGuardedAttributes();
        } else {
            return $this->replaceFillableAttributes();
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

    private function replaceDates()
    {
        $dates_attributes = $this->getDateAttributes()->merge($this->dates)->keys();
        $dates = <<<EOT
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected \$dates = $dates_attributes;
EOT;
        if ($dates_attributes->isEmpty()) {
            $this->stub = str_replace('{{datesFields}}', '', $this->stub);
        } else {
            $this->stub = str_replace('{{datesFields}}', $dates, $this->stub);
        }
        return $this;
    }

    private function replaceRelationShips()
    {
        collect($this->schema['relations'])->each(function ($relation, $key) {
            $relationMethodName = 'createRelation' . ucfirst(camel_case($relation['type']));
            if (method_exists($this, $relationMethodName)) {
                $relationName = strtolower($key);
                $class = $this->config['namespace'] . '\\' . $relation['class'];
                $this->relations[] = $this->$relationMethodName($relationName, $class);
            }
        });

        $this->stub = str_replace('{{relationships}}', implode("\n", $this->relations), $this->stub);
        return $this;
    }

    private function createRelationBelongsTo($relationName, $class)
    {
        $currentClass = strtolower($this->data['class_name']);
        return $relation = <<<EOT

    /**
    * Get the $relationName that owns the $currentClass.
    */
    public function $relationName()
    {
        return \$this->belongsTo($class::class);
    }
EOT;
    }

    private function createRelationHasMany($relationName, $class)
    {
        $currentClass = strtolower($this->data['class_name']);
        return $relation = <<<EOT

    /**
    * Get the $relationName for the $currentClass.
    */
    public function $relationName()
    {
        return \$this->hasMany($class::class);
    }
EOT;
    }
}
