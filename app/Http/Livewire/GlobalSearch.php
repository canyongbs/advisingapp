<?php

namespace App\Http\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;
use Symfony\Component\Finder\Finder;

class GlobalSearch extends Component
{
    public string $search = '';

    public array $results = [];

    protected $rules = [
        'search' => 'required|min:1',
    ];

    public function updatedSearch()
    {
        $this->reset('results');
        $this->validateOnly('search');
        $this->results = collect(
            iterator_to_array($this->getSearchResults(), false)
        )->mapToGroups(function ($item) {
            return [$item['modelName'] => $item];
        })->toArray();
    }

    public function resetForm()
    {
        $this->reset(['search', 'results']);
    }

    public function render()
    {
        return view('livewire.global-search');
    }

    public function getSearchResults()
    {
        foreach ($this->getSearchableModels() as $model) {
            $query = (new $model())->query();

            foreach ($model::$search as $column) {
                $query->orWhere($column, 'LIKE', '%' . $this->search . '%');
            }

            yield from $query->limit(5)
                ->cursor()
                ->map(function ($resource) {
                    $model_key = Str::camel(class_basename($resource));
                    $route_key = Str::plural(Str::kebab(class_basename($resource)));
                    $fields    = [];

                    foreach ($resource::$search as $field) {
                        $field_key                 = sprintf('cruds.%s.fields.%s', $model_key, $field);
                        $fields[trans($field_key)] = $resource->{$field};
                    }

                    return [
                        'modelName' => trans(sprintf('cruds.%s.title', $model_key)),
                        'linkTo'    => route(sprintf('admin.%s.show', $route_key), $resource),
                        'fields'    => $fields,
                    ];
                });
        }
    }

    protected function getSearchableModels(): array
    {
        $namespace = app()->getNamespace();
        $models    = [];

        foreach ((new Finder())->in(app_path())->files() as $model) {
            $model = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($model->getPathname(), app_path() . DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($model, Model::class)
                && Gate::allows(sprintf('%s_show', Str::snake(Str::afterLast($model, '\\'))))
                && ! empty($model::$search)
            ) {
                $models[] = $model;
            }
        }

        return collect($models)->sort()->all();
    }
}
