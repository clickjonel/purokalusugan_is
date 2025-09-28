
Create Model with Migration,Controller,Seeder
php artisan make:model Programs -mcs
php artisan migrate
php artisan cache:clear
php artisan config:clear


Sanctum
Session_Driver=file

Session
Session_Driver=database

Migrate Database into PKPulse Database then seed the database
php artisan migrate:fresh --database=pkpulse --seed


php artisan serve --host=210.213.80.10 --port=9500
