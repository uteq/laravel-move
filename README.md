# Laravel Move | An admin panel powered by Livewire and Jetstream

[![Latest Version on Packagist](https://img.shields.io/packagist/v/uteq/laravel-move.svg?style=flat-square)](https://packagist.org/packages/uteq/laravel-move)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/uteq/laravel-move/run-tests?label=tests)](https://github.com/uteq/laravel-move/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/uteq/laravel-move.svg?style=flat-square)](https://packagist.org/packages/uteq/laravel-move)

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
<img src="https://uteq.nl/images/move-example.png" />

## Todo
- Translations
- Package dependencies
- Tests

## Support us
To best support is by improving this package. There is still a lot work to be done.
For example:
- Test coverage
- Fields extension
- Stubs and Class generators

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
https://jetstream.laravel.com/1.x/installation.html#installing-jetstream

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Uteq\Move\MoveServiceProvider" --tag="config"
```

You can publish the view files with:
```bash
php artisan vendor:publish --provider="Uteq\Move\MoveServiceProvider" --tag="views"
```

## Usage

### Creating your first resource
Start by creating your first Move Resource. You can generate it or use the example below:

#### Generate your reso§urce
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
            Text::make('Naam', 'name'),

            Text::make('Email', 'email')
                ->hideWhenUpdating()
                ->requiredOnCreateOnly()
                ->creationRules(['required', 'string', 'email', 'max:255', 'unique:users']),

            Password::make('Password', 'password')
                ->creationRules($this->passwordRules())
                ->required(function($request, $model) {
                    return ! ( $model->id ?? false );
                }),

            Password::make('Confirm password', 'password_confirmation')
                ->hideFromIndex()
                ->hideFromDetail()
                ->required(function($request, $model) {
                    return ! ( $model->id ?? false );
                }),

            Status::make('Email verified?', 'email_verified_at', function ($value) {
                return $value !== null;
            })->hideFromForm(),
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

    public function icon()
    {
        return 'heroicon-o-users';
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

## In depth

### Resolving a resource
Resolving a resource means loading the concrete implementation of your Resource class.
You can do this by providing the name of your resource:

```php
use \Uteq\Move\Facades\Move;

Move::resolveResource('resources.your-resource');
```

### Overwriting the default $actionHandlers
Action handlers are classes that make it possible to store and delete your resources.
Move provides two default action handlers `Uteq\Move\DomainActions\StoreResource` and `Uteq\Move\DomainActions\DeleteResource`.
You are able to overwrite these handlers from your Resource and by default.

#### Overwriting the default $actionHandlers system wide
Overwrite the action handlers from a ServiceProvider
```php
use Uteq\Move\Resource;

public function register()
{
    Resource::$defaultActionHandlers = [
        'update' => StoreSystemWide::class,
        'create' => StoreSystemWide::class,
        'delete' => DestroySystemWide::class,
    ];
}
````

#### Overwriting the default $actionHandlers from your resource
Simply add the $actionHandlers to your resource:
```php
use Uteq\Move\Resource;

class CustomResource extends Resource
{
    public array $actionHandlers = [
        'update' => StoreCustomResource::class,
        'create' => StoreCustomResource::class,
        'delete' => DestroyCustomResource::class,
    ];
}
```  
This will overwrite the system wide action handlers.

### Hooks
#### Before save
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

#### After save
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
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
