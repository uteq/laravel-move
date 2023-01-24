# Please use Filament over this package. This will package will be archived soon.

# Laravel Move | An admin panel powered by Livewire and Jetstream

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uteq/laravel-move.svg?style=flat-square)](https://packagist.org/packages/uteq/laravel-move)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/uteq/laravel-move/run-tests?label=tests)](https://github.com/uteq/laravel-move/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/uteq/laravel-move.svg?style=flat-square)](https://packagist.org/packages/uteq/laravel-move)

<img src="https://uteq.nl/images/move/move-logo.png" />

<b>This package is still in development and does not have a complete test suite.</b>

Move makes it very easy to create your own Admin Panel using Laravel and Livewire. 
This package was heavily inspired bij Laravel Nova. And works practically the same, except for some missing features.
 
Here is an example of how you can use it:
```php
<?php

namespace App\Move;

use Uteq\Move\Fields\Id;
use Uteq\Move\Fields\Text;
use Uteq\Move\Resource;

class User extends Resource
{
    public static $model = \App\Models\User::class;

    public static string $title = 'name';

    public function fields()
    {
        return [
            Id::make(),

            Text::make('Name', 'name'),
        ];
    }
}
```

And this is a basic example with a user:
<img src="https://uteq.nl/images/move/move-example.png" />

## Todo
- Package dependencies
- Tests

## Support us
To best support is by improving this package. There is still a lot work to be done.
For example:
- Documentation
-- Setup
-- Fields (Also: Panel and Step)
-- Search
-- Actions
-- Permissions
-- Customizations
-- Reuseable (Move re-usable in many ways)
- Test coverage
- Fields extension (adding all sorts of fields)
- Stubs and Class generators
- Cards (like Laravel Nova cards)

## Installation

You can install the package via composer:

```bash
composer require uteq/laravel-move
```

Laravel Move will add Jetstream to your vendor folder, but will not install it automatically.
So, for your convenience we tailor made a command that will install Jetstream and bootstrap the Move Admin Panel.
Because Move uses Livewire as the preferred stack you do not have to supply the stack. In addition, you may use the --teams switch to enable team support:

```bash
php artisan move:install --team
``` 

To finalize your installation run:

```bash
php artisan migrate
```

### Optional

#### Configure Jetstream
For more Jetstream related setup, please check:
https://jetstream.laravel.com/2.x/installation.html#installing-jetstream

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Uteq\Move\MoveServiceProvider" --tag="move-config"
```

You can publish the view files with:
```bash
php artisan vendor:publish --provider="Uteq\Move\MoveServiceProvider" --tag="move-views"
```

## Usage

### Creating your first resource
Start by creating your first Move Resource. You can generate it or use the example below:

#### Generate your resource
```bash
php artisan move:resource User --model=User
```

#### Use example resource

```php
<?php

namespace App\Move;

use Uteq\Move\Fields\Id;
use Uteq\Move\Fields\Text;
use Uteq\Move\Resource;

class User extends Resource
{
    public static $model = \App\Models\User::class;

    public static string $title = 'name';

    public function fields()
    {
        return [
            Id::make(),

            Text::make('Naam', 'name')
                ->rules(['required', 'string', 'max:255'])
                ->required(),

            Text::make('E-mailadres', 'email')
                ->hideWhenUpdating()
                ->requiredOnCreateOnly()
                ->creationRules(['required', 'string', 'email', 'max:255', 'unique:users']),

            Status::make('E-mail bevestigd?', 'email_verified_at', fn ($value) => $value !== null)
                ->hideFromForm(),

            Panel::make('Wachtwoord wijzigen', [
                Password::make('Wachtwoord', 'password', null)
                    ->creationRules($this->passwordRules())
                    ->requiredOnCreateOnly(),

                Password::make('Wachtwoord bevestigen', 'password_confirmation')
                    ->hideFromIndex()
                    ->hideFromDetail()
                    ->onlyForValidation(fn ($value, $field, $model) => $model->id)
                    ->requiredOnCreateOnly(),
            ])
            ->nameOnCreate('Wachtwoord')
            ->nameOnUpdate('Wachtwoord wijzigen'),

            Panel::make('Rol kiezen', [
                Role::make('Rol', 'role'),
            ]),
        ];
    }

    public function filters()
    {
        return [];
    }

    public function actions()
    {
        return [];
    }
}
```


### Route prefix
Move out of the box adds a prefix to your resources, that way it will never interfere with your own routes.
The default is `move`.
You can change the default prefix by overwriting it at your local MoveServiceProvider at `App\Providers\MoveServiceProvider`:

```php
use Illuminate\Support\Facades\Route;

function register()
{
    Route::move('my-prefix');
}
```

### Manually Registering Resource Namespaces
The default namespace for Move is `App\Move`. You are also able to register the Move Resources wherever you like.
You can Bootstrap this namespace in the following way

```php
use Uteq\Move\Facades\Move;

public function register()
{
    Move::resourceNamespace('App\\Resources', 'resources');
}
```

This will automatically create the namespace for the routes.
The default route for this namespace will be:

```
https://move.test/move/resources/your-resource
```

The resource default name will than be:

```
resources.your-resource
```

### Using a json field to store data
If you work a lot with data structures that cannot be clearly  defined in a mysql database structure. You will probably opt for something like a json field.

First add your json field to your table.
```php
$table->json('meta')->nullable();
```

Than make sure you cast it properly in you model
```php
protected $casts ['meta' => 'json'];
```

At last, add a field to your resource

```php
Text::make('My meta value', 'meta.my_value'),
```

## Resources

### Supported Field types
These are the currently supported fields in Move:
- Country
- Date
- Files
- Id
- Number
- Password
- Select (with filter search)
- Status
- Text
- Textarea
- Editor

## Sidebar
Move automatically registers resources to the sidebar. 
By default there are two ways to show your sidebar resources.
Grouped and flat. The default is grouped  and is published with the \App\Providers\MoveServiceProvider.

```php
Move::useSidebarGroups(true);
```

To use the flat display, just change to
```php
Move::useSidebarGroups(false);
```

When you use the sidebar groups you should also set the resource property:

```php
class User extends \Uteq\Move\Resource
{
    public static string $group = 'admin';
}
```

### Ordering sidebar items
Currently, this is not a supported feature, feel free to create a PR. Or you can simply
overwrite the components/sidebar-menu.blade.php file.

### Ordering sidebar by namespace
Whenever you would prefer to order your sidebar by the given namespace just use
the grouped sidebar approach.
If not you can always overwrite the components/sidebar-menu.blade.php file or create a PR. 

### Overwrite the sidebar menu logo
```blade
<x-move-sidebar>
    <x-slot name="logo">
        <a class="text-center" href="/">
            <h1 class="text-2xl text-white font-black">{{ config('app.name') }}</h1>
        </a>
    </x-slot>
</x-move-sidebar>
```

### Overwriting the sidebar menu items
You can completely overwrite the sidebar items. And the logo. 
```blade
<x-move-sidebar :keep-not-custom="false" :with-padding="false">
    <!-- You can also add you own html + css here, move just has a link component -->
    <x-move-sidebar.link href="{{ route('dashboard') }}" alt-active="admin/dashboard/*">
        Dashboard
    </x-move-sidebar.link>
</x-move-sidebar>
```

You can also keep the automatically generated sidebar items and
add you own before the automatic created. This is the default behavior.
```blade
<x-move-sidebar>    
    <x-move-sidebar.link href="{{ route('dashboard') }}" alt-active="admin/dashboard/*">
        Dashboard
    </x-move-sidebar.link>
</x-move-sidebar>
```

## In depth

### Resolving a resource
Resolving a resource means loading the concrete implementation of your Resource class.
You can do this by providing the name of your resource:

```php
use \Uteq\Move\Facades\Move;

Move::resolveResource('resources.your-resource');
```

### Overwriting the default $actionHandlers
Action handlers are classes that make it possible to store and delete your resources the way you prefer. By default Move will have its own default logic.
Move provides two default action handlers `Uteq\Move\DomainActions\StoreResource` and `Uteq\Move\DomainActions\DeleteResource`.
You are able to overwrite these handlers from your Resource and by default.

#### Creating your own Action handler
```php
class StoreHandler
{
    public function __invoke(Model $model, array $input = [])
    {
        //... Basic validation
        
        $model->fill($input)->save();
        
        return $model;
    }
}
```

You can also extend the default StoreResource, but this is not mandatory.

#### Overwriting the default $actionHandlers system wide
Overwrite the action handlers from a ServiceProvider.
```php
use Uteq\Move\Resource;

public function register()
{
    Resource::$defaultActionHandlers = [
        'update' => StoreResource::class,
        'create' => StoreResource::class,
        'delete' => DeleteResource::class,
    ];
}
````

#### Overwriting the default $actionHandlers from your resource
Simply add the $actionHandlers to your resource:
```php
use Uteq\Move\Resource;
use App\Actions\CustomResource;

class CustomResource extends Resource
{
    public array $actionHandlers = [
        'create' => CustomResource\Create::class,
        'update' => CustomResource\Update::class,
        'delete' => CustomResource\Delete::class,
    ];
}
```  
This will overwrite the resource specific action handlers.

### Hooks
#### Model Before save
The preferred way to hook into the before save is using the default Laravel events https://laravel.com/docs/eloquent#events.
You can also hook into the Store action by adding a beforeSave method that provides callables
```php
public function beforeSave()
{
    return [
        fn($resource, $model, $data) => $data['rand'] = rand(1, 99),
        function($resource, $model, $data) {
            return $data['rand'] = rand(1, 99);
        },
        new MyCustomBeforeSaveAction,
    ];
}
```

#### Model After save
The preferred way to hook into the after save is using the default Laravel events https://laravel.com/docs/eloquent#events.
You can also hook into the Store action by adding a afterSave method that provides callables
```php
public function afterSave()
{
    return [
        fn($resource, $model, $data) => $data['rand'] = rand(1, 99),
        function($resource, $model, $data) {
            return $data['rand'] = rand(1, 99);
        },
        new MyCustomAfterSaveAction,
    ];
}
```

#### Field Before save
Whenever you need to change to way a field stores (creates or updates) it's data.
You can hook into the `beforeStore` method.
Every field has this method.

```php
Text::make('Name', 'name')
    ->beforeStore(function($value) {
        // ... Mutate the fields value to any given format.
        return $value;
    });
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Nathan Jansen](https://github.com/nathanjansen)
- [Leo Flapper](https://github.com/leoflapper)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
