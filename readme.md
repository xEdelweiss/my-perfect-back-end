## My Perfect BackEnd

### base installation

    composer install/update
    php artisan key:generate

### tags

    php artisan vendor:publish --provider="Conner\Tagging\Providers\TaggingServiceProvider"
    php artisan migrate

### ide-helper

    php artisan vendor:publish --provider=barryvdh/laravel-ide-helper --tag=config

_Built with Laravel PHP Framework._