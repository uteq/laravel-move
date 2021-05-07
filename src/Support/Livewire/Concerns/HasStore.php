<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\ObjectPrybar;

trait HasStore
{
    public $inModal = false;

    public $shouldRedirect = true;

    protected $redirectEndpoints = [
        'create' => 'index',
        'update' => 'index',
        'cancel' => 'index',
    ];

    /**
     * When you prefer different action methods
     * You can overwrite them by overwriting this property.
     *
     * @var string[]
     */
    protected $actionsMethods = [
        'create' => 'create',
        'update' => 'update',
        'cancel' => 'cancel',
    ];

    public function redirects(): array
    {
        // Overwrite method to add your own endpoints
        return [];
    }

    public function rules($model = null): array
    {
        // Overwrite method to add your own rules
        return [];
    }

    public function hasStoreRefreshModel()
    {
        if ($this->{$this->property}->id) {
            $this->{$this->property}->refresh();
        }
    }

    public function save()
    {
        $this->hasStoreRefreshModel();

        $store = $this->store;

        if (method_exists($this, 'beforeStore')) {
            $store = $this->beforeStore($store);
        }

        $data = [
            'fields' => $this->resolveAndMapFields($this->model, $store),
        ];

        session()->flash('status', __('Saved :resource', ['resource' => $this->label()]));

        /** @psalm-suppress InvalidArgument */
        $action = $this->{$this->property}->id
            ? app()->call([$this, $this->actionsMethods['update']], $data)
            : app()->call([$this, $this->actionsMethods['create']], $data);

        $this->emit('saved');

        if (method_exists($this, 'afterStore')) {
            $result = $this->afterStore($action);

            return $result ? $result : $action;
        }

        return $action;
    }

    public function updateWithoutRedirect(array $fields, array $rules = null)
    {
        $this->disableRedirect();
        $this->update($fields, $rules);
        $this->enableRedirect();

        return $this;
    }

    public function update(array $fields, array $rules = null)
    {
        $this->customValidate($fields, $rules ?: $this->rules($this->{$this->property}));

        // This ensures that the fields data is formatted as the user wants it
        //  this will for example prevent a empty value to be seen as 0
        //  and skipped by the rules
        $data = $this->resource()
            ->toDataTransferObject($fields, 'fromLivewire');

        $fields = is_array($data) ? $data : $data->toArray();

        $this->handleResourceAction('update', $fields);

        return $this->maybeRedirectFromAction('update');
    }

    public function create(array $fields, array $rules = null)
    {
        $this->customValidate($fields, $rules ?: $this->rules($this->{$this->property}));

        // This ensures that the fields data is formatted as the user wants it
        //  this will for example prevent a empty value to be seen as 0
        //  and skipped by the rules
        $data = $this->resource()
            ->toDataTransferObject($fields, 'fromLivewire');

        $fields = is_array($data) ? $data : $data->toArray();

        $this->handleResourceAction('create', $fields);

        return $this->maybeRedirectFromAction('create');
    }

    public function cancel()
    {
        if ($this->actionsMethods['cancel'] !== 'cancel') {
            return app()->call([$this, $this->actionsMethods['cancel']]);
        }

        return $this->maybeRedirectFromAction('cancel');
    }

    public function cancelRoute()
    {
        return $this->endpointRoute('index');
    }

    public function customValidate(array $fields, array $rules, array $messages = [], $customAttributes = [])
    {
        $this->resetErrorBag();

        // Ensures that when a multi dimensional array is used, and flattened, it still validates.
        $fields = collect($fields)
            ->mapWithKeys(fn ($value, $field) => [str_replace('.', '---', $field) => $value])
            ->toArray();

        $rules = collect($rules)
            ->mapWithKeys(function ($value, $field) use ($fields) {
                // If the field exists as array, no need to change the rule
                if (isset($fields[Str::before(Str::after($field, 'store.'), '.')])) {
                    return [$field => $value];
                }

                return [str_replace('.', '---', $field) => $value];
            })
            ->toArray();

        try {
            $valid = Validator::make($fields, $rules)->validate();
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $target = new ObjectPrybar($e->validator);

            $messages = new MessageBag(collect($messages->getMessages())
                ->mapWithKeys(fn ($message, $key) => ['store.' . $key => $message])
                ->toArray());

            $target->setProperty('messages', $messages);

            throw $e;
        }

        return collect($valid)
            ->mapWithKeys(fn ($value, $key) => [
                str_replace('---', '.', $key) => $value,
            ])
            ->toArray();
    }

    protected function getRules()
    {
        $preparedRules = [];
        foreach ($this->rules() as $key => $rule) {
            $preparedRules[$this->property . '.' . $key] = $rule;
        }

        return $preparedRules;
    }

    public function maybeRedirectFromAction($action)
    {
        if (! $this->shouldRedirect) {
            return false;
        }

        $endpoint = $this->endpoint($action);

        if (! $endpoint) {
            return null;
        }

        return redirect($this->endpointRoute($endpoint));
    }

    private function endpoint($key, $default = null)
    {
        return $this->endpoints()[$key] ?? $default;
    }

    private function endpoints()
    {
        return array_replace_recursive(
            $this->redirectEndpoints,
            $this->redirects(),
            method_exists($this->resource(), 'redirects')
                ? $this->resource()->redirects()
                : []
        );
    }

    public function endpointRoute($endpoint)
    {
        if ($endpoint instanceof \Closure) {
            return $this->endpointRoute($endpoint($this));
        }

        if (! is_string($endpoint)) {
            throw new \Exception(sprintf(
                '%s: syntax error, unexpected `$endpoint` type `%s`, it should be a string',
                $endpoint,
                gettype($endpoint)
            ));
        }

        $routeName = $this->routeNameFromEndpoint($endpoint);

        $params = array_replace([
            'resource' => $this->resource,
            'model' => $this->model,
        ], $this->routeParams());

        return Route::has($routeName)
            ? route($routeName, $params)
            : (
                Route::has($endpoint)
                    ? route($endpoint, $params)
                    : $endpoint
            );
    }

    private function routeParams()
    {
        $params = [];
        if ($this->resource) {
            $params['resource'] = $this->resource;
        }

        return $params;
    }

    public function routeNameFromEndpoint($endpoint)
    {
        return $this->baseRoute . '.' . $endpoint;
    }

    public function disableRedirect()
    {
        $this->shouldRedirect = false;

        return $this;
    }

    public function enableRedirect()
    {
        $this->shouldRedirect = true;

        return $this;
    }

    public function flattenStore($keepArrays = false)
    {
        $this->store = Arr::dot(
            collect($this->store)
                ->when(! $keepArrays, fn ($store) => $store->filter(fn ($value) => ! is_array($value)))
                ->toArray()
        );

        $this->model->store = $this->store;

        return $this;
    }
}
