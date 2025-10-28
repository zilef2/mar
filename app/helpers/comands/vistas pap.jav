//misterdebug - crud-generator-laravel
(example-project: IMI)

//just model
php artisan make:model LosPromps -all

//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
//its donest need a Model -> materia_user

php artisan make:0import PersonalImport --model=User

php artisan make:export TodaBDExport

// node
vue-datapicker
// laravel
composer require maatwebsite/excel

// parte generica
