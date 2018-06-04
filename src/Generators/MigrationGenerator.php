<?php

namespace Kodeloper\Generator\Generators;

use Illuminate\Support\Carbon;


class MigrationGenerator extends BaseGenerator
{
    protected $fields = [];

    /**
     * Get the model stub file path for the generator.
     *
     * @return string
     */
    protected function getStubFilePath()
    {
        return config('generator.custom_stubs')
            ? config('generator.custom_stubs.path') . '/Migration.stub'
            : __DIR__ . '/../Stubs/Migration.stub';
    }

    public function fromSchema(array $data)
    {
        $this->stub = file_get_contents($this->getStubFilePath());
        $this->schema = $this->getSchema()[$data['model']];
        $this->config = $this->getPackageConfig()['migrations'];

        $this->data = $data;
        $this->data['class_name'] = $this->data['model'];

        $this->replaceClass()
             ->replaceTable()
             ->replaceFields();

        $this->generateFile($this->config['path'], Carbon::now()->format('Y_m_d_His') . "_create_{$this->data['table']}_table.php");

        return $this->stub;
    }

    private function replaceClass()
    {
        $this->stub = $stub = str_replace('{{class}}', "Create{$this->data['model']}Table", $this->stub);
        return $this;
    }

    private function replaceTable()
    {
        $this->stub = $stub = str_replace('{{table}}', $this->data['table'], $this->stub);
        return $this;
    }

    private function replaceFields()
    {
        $this->getFields()->map(function ($attributes, $field) {
            $parser = 'parse' . ucfirst(camel_case($attributes['type']) . 'Field');
            if (method_exists($this, $parser)) {
                $this->fields[] = $this->$parser($field, $attributes);
            }
        });

        $this->stub = $stub = str_replace('{{fields}}', implode("\t\t" . PHP_EOL, $this->fields), $this->stub);

        return $this;
    }

    private function getFields()
    {
        return collect($this->schema['attributes']);
    }

    private function parseAutoincrementField($field, $attributes)
    {
        return "\$table->increments('{$field}');";
    }

    private function parseStringField($field, $attributes)
    {
        return "            \$table->string('{$field}');";
    }
}
