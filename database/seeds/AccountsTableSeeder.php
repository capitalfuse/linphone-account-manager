<?php

use App\Models\Profile;
use App\Models\Account;
use App\Models\Password;
use Illuminate\Database\Seeder;
use jeremykenedy\LaravelRoles\Models\Role;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $password = new Password();
        $profile = new Profile();
        $adminRole = Role::whereName('Admin')->first();
        $userRole = Role::whereName('User')->first();

        // Seed test admin
        $seededAdminEmail = 'admin@admin.com';
        $user = Account::where('email', '=', $seededAdminEmail)->first();
        if ($user === null) {
            $user = Account::create([
                'username'                       => $faker->userName,  
                'first_name'                     => $faker->firstName,
                'last_name'                      => $faker->lastName,
                'domain'                         => $faker->domainName,
                'email'                          => $seededAdminEmail,
                'token'                          => str_random(64),
                'activated'                      => true,
                'signup_confirmation_ip_address' => $faker->ipv4,
                'admin_ip_address'               => $faker->ipv4,
                'user_agent'                     => $faker->userAgent,
                'ip_address'                     => $faker->ipv4,
                'creation_time'                  => $faker->dateTime,
            ]);

            $password = Password::create ([
                'account_id'       => $user->id,
                'password'         => hash('sha256', $user->username.':'.$user->domain.':testtest'),
                'algorithm'        => 'SHA-256',
            ]);

            $user->password()->save($password);
            $user->profile()->save($profile);
            $user->attachRole($adminRole);
            $user->save();
        }

        // Seed test user
        $user = Account::where('email', '=', 'user@user.com')->first();
        if ($user === null) {
            $user = Account::create([
                'username'                       => $faker->userName,
                'first_name'                     => $faker->firstName,
                'last_name'                      => $faker->lastName,
                'domain'                         => $faker->domainName,
                'email'                          => 'user@user.com',
                'token'                          => str_random(64),
                'activated'                      => true,
                'signup_ip_address'              => $faker->ipv4,
                'signup_confirmation_ip_address' => $faker->ipv4,
                'user_agent'                     => $faker->userAgent,
                'ip_address'                     => $faker->ipv4,
                'creation_time'                  => $faker->dateTime,
            ]);

            $password = Password::create ([
                'account_id'       => $user->id,
                'password'         => hash('sha256', $user->username.':'.$user->domain.':testtest'),
                'algorithm'        => 'SHA-256',
            ]);

            $user->password()->save($password);
            $user->profile()->save(new Profile());
            $user->attachRole($userRole);
            $user->save();
        }

        // Seed test users
        // $user = factory(App\Models\Profile::class, 5)->create();
        // $users = Account::All();
        // foreach ($users as $user) {
        //     if (!($user->isAdmin()) && !($user->isUnverified())) {
        //         $user->attachRole($userRole);
        //     }
        // }
    }
}
