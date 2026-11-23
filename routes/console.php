<?php

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('program-head:create', function () {
    $name = trim($this->ask('Program Head full name'));
    $email = Str::lower(trim($this->ask('College email address')));

    $validator = Validator::make(
        ['name' => $name, 'email' => $email],
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ],
    );

    if ($validator->fails()) {
        $this->error($validator->errors()->first());

        return Command::FAILURE;
    }

    $password = $this->secret('Temporary password (at least 12 characters)');

    if (Str::length($password) < 12) {
        $this->error('The password must contain at least 12 characters.');

        return Command::FAILURE;
    }

    if (! hash_equals($password, $this->secret('Confirm temporary password'))) {
        $this->error('The passwords did not match.');

        return Command::FAILURE;
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => User::ROLE_PROGRAM_HEAD,
    ]);

    $this->info("Program Head account created for {$user->email}.");

    return Command::SUCCESS;
})->purpose('Create a Program Head account');

Artisan::command('administrator:create', function () {
    $name = trim($this->ask('Administrator full name'));
    $email = Str::lower(trim($this->ask('Administrator email address')));

    $validator = Validator::make(
        ['name' => $name, 'email' => $email],
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ],
    );

    if ($validator->fails()) {
        $this->error($validator->errors()->first());

        return Command::FAILURE;
    }

    $password = $this->secret('Temporary password (at least 12 characters)');
    if (Str::length($password) < 12) {
        $this->error('The password must contain at least 12 characters.');

        return Command::FAILURE;
    }
    if (! hash_equals($password, $this->secret('Confirm temporary password'))) {
        $this->error('The passwords did not match.');

        return Command::FAILURE;
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => User::ROLE_ADMINISTRATOR,
    ]);

    $this->info("Administrator account created for {$user->email}.");

    return Command::SUCCESS;
})->purpose('Create an administrator account');

Artisan::command('registrar:create', function () {
    $name = trim($this->ask('Registrar full name'));
    $email = Str::lower(trim($this->ask('Registrar email address')));

    $validator = Validator::make(
        ['name' => $name, 'email' => $email],
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ],
    );

    if ($validator->fails()) {
        $this->error($validator->errors()->first());

        return Command::FAILURE;
    }

    $password = $this->secret('Temporary password (at least 12 characters)');
    if (Str::length($password) < 12) {
        $this->error('The password must contain at least 12 characters.');

        return Command::FAILURE;
    }
    if (! hash_equals($password, $this->secret('Confirm temporary password'))) {
        $this->error('The passwords did not match.');

        return Command::FAILURE;
    }

    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'role' => User::ROLE_REGISTRAR,
    ]);

    $this->info("Registrar account created for {$user->email}.");

    return Command::SUCCESS;
})->purpose('Create a registrar account');
