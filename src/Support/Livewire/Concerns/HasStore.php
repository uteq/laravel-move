<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Livewire\ObjectPrybar;

trait HasStore
{
    public $inModal = false;

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

    public function save()
    {
        $this->{$this->property}->refresh();

        if (method_exists($this, 'beforeStore')) {
            $this->beforeStore($this->{$this->property});
        }

        $data = [
            'fields' => $this->resolveAndMapFields($this->model),
        ];

        /** @psalm-suppress InvalidArgument */
        return $this->{$this->property}->id
            ? app()->call([$this, $this->actionsMethods['update']], $data)
            : app()->call([$this, $this->actionsMethods['create']], $data);
    }

    public function update(array $fields)
    {
        $this->customValidate($fields, $this->rules($this->{$this->property}));

        // This ensures that the fields data is formatted as the user want it
        //  this will for example prevent a empty value to be seen as 0
        //  and skipped by the rules
        $data = $this->resource()
            ->toDataTransferObject($fields, 'fromLivewire');

        $fields = is_array($data) ? $data : $data->toArray();

        $this->handleResourceAction('update', $fields);

        return $this->maybeRedirectFromAction('update');
    }

    public function create(array $fields)
    {
        $this->customValidate($fields, $this->rules($this->{$this->property}));

        // This ensures that the fields data is formatted as the user want it
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
        try {
            return Validator::make($fields, $rules)->validate();
        } catch (ValidationException $e) {
            $messages = $e->validator->getMessageBag();
            $target = new ObjectPrybar($e->validator);

            $messages = new MessageBag(collect($messages->getMessages())
                ->mapWithKeys(fn ($message, $key) => [$this->property . '.' . $key => $message])
                ->toArray());

            $target->setProperty('messages', $messages);

            throw $e;
        }
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
        return array_replace_recursive($this->redirectEndpoints, $this->redirects());
    }

    public function endpointRoute($endpoint)
    {
        if ($endpoint instanceof \Closure) {
            return $this->endpointRoute($endpoint());
        }

        if (! is_string($endpoint)) {
            throw new \Exception(sprintf(
                '%s: syntax error, unexpected `$endpoint` type `%s`, it should be a string',
                $endpoint,
                gettype($endpoint)
            ));
        }

        $routeName = $this->routeNameFromEndpoint($endpoint);

        return Route::has($routeName)
            ? route($routeName, $this->routeParams())
            : (
                Route::has($endpoint)
                    ? route($endpoint, $this->routeParams())
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
}
