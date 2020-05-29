<?php

use jeremykenedy\LaravelRoles\Models\Role;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Account::class, function (Faker\Generator $faker) {

    $userRole = Role::whereName('User')->first();

    return [
        'username'                          => $faker->unique()->userName,
        'first_name'                        => $faker->firstName,
        'last_name'                         => $faker->lastName,
        'domain'                            => $faker->domainName,
        'email'                             => $faker->unique()->safeEmail,
        'token'                             => str_random(64),
        'activated'                         => true,
        'remember_token'                    => str_random(10),
        'signup_ip_address'                 => $faker->ipv4,
        'signup_confirmation_ip_address'    => $faker->ipv4,
        'user_agent'                        => $faker->userAgent,
        'ip_address'                        => $faker->ipv4,
        'creation_time'                     => $faker->dateTime,
    ];
});

$factory->define(App\Models\Password::class, function (Faker\Generator $faker) use ($factory) {
    $account = $factory->create(App\Models\Account::class);
    return [
        'account_id'       => $account->id,
        'password'         => hash('sha256', $account->username.':'.$account->domain.':testtest'),
        'algorithm'        => 'SHA-256',
    ];
});

$factory->define(App\Models\Profile::class, function (Faker\Generator $faker) {
    return [
        'account_id'       => factory(App\Models\Account::class)->create()->id,
        'theme_id'         => 1,
        'location'         => $faker->streetAddress,
        'bio'              => $faker->paragraph(2, true),
        'twitter_username' => $faker->userName,
        'github_username'  => $faker->userName,
    ];
});
