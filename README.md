# purokalusugan_is
Purokalusugan Information System

How to Start Api(Fresh Download/Clone)
  1. Open terminal the go to pkp_api directory(cd pkp_api)
  2. Enter composer install
  3. Enter php artisan serve

# Needed items for backend
1. you need nodeJS v20 and higher
2. you need composer.js
https://getcomposer.org/download/
download and run composer-setup.exe
3. after installing
go to command line issue
composer install
wait to finish

4. start the api with
php artisan serve

# how to create/migrate tables using migration
1. update your env file with the database you want to work on
example:
inside .env

PKP_DB_CONNECTION=pkpulse
PKP_DB_HOST=127.0.0.1
PKP_DB_PORT=3306
PKP_DB_DATABASE=pkpulse
PKP_DB_USERNAME=root
PKP_DB_PASSWORD=

2. update the database file inside config/database.php
set the default 
    'default' => env('DB_CONNECTION', 'pkpulse'),
    to the database you plan to use

    then inside connections array variable include your database
    'connections' => [
    'pkpulse' => [
            'driver' => 'mysql',
            'host' => env('PKP_DB_HOST'),
            'port' => env('PKP_DB_PORT'),
            'database' => env('PKP_DB_DATABASE'),
            'username' => env('PKP_DB_USERNAME'),
            'password' => env('PKP_DB_PASSWORD'),
        ],
    ]

3. then run a shortcut to create model, migration, controller seeder

run this on your command line

php artisan make:model Programs -mcs
note: the model is Programs -mcs will create migration, controller and seeder files
then edit accordingly the generated files
4. # this will add/create tables automatically to your database
1. create a blank database on phpmyadmin for this example :create one for pkpulse
2. prepare the following Model, Controller, Migration by issuing the command
php artisan make:model Programs -mcs
Note: from the command above it will automatically create the files
ProgramModel.php
ProgramController.php
ProgramMigration.php
ProgramSeeder.php

complete the content of the files accordingly
2. then run
php artisan migrate:fresh --database=pkpulse --seed
on your command line

# how to apply MVC framework Model, View, Controller for postman/thunderclient testing
1. create a model
example:
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkp_indicator extends Model
{
    protected $connection = 'pkpulse';
    protected $fillable = [        
        'program_id',
        'indicator_code',
        'indicator_name',
        'indicator_description',
        'indicator_status',
        'indicator_scope'
    ];
}

2. create a controller
example:
<?php

namespace App\Http\Controllers;
use App\Models\Pkp_indicator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpIndicatorController extends Controller
{
    // create
    public function createIndicator(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'program_id' => 'required|integer',
            'indicator_code' => 'string',
            'indicator_name' => 'string',
            'indicator_description' => 'string',
            'indicator_status' => 'integer',
            'indicator_scope' => 'integer'            
        ]);
        $pkpIndicator=Pkp_indicator::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$pkpIndicator
        ], 201);
    }

    // update


    // list


    //delete
}



3. call the functions from the controller to api.php
example:
<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PkpIndicatorController;
use App\Http\Controllers\ProgramsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'indicator'
], function () {
    Route::post('/create',[PkpIndicatorController::class,'createIndicator']);
    // other functions here from controller
});
4. create a postman/thunderclient request
for this example:
POST http://127.0.0.1:8000/api/indicator/create
HTTP Headers 
accept: application/json
Body: 
{
"indicator_id": 1,
"program_id": 1,
"indicator_code": "SI-001",
"indicator_name": "Sample Indicator Name",
"indicator_description": "This is a sample indicator description",
"indicator_status": 1,
"indicator_scope": 1
}
