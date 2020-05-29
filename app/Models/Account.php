<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;

class Account extends Authenticatable
{
    use HasRoleAndPermission;
    use Notifiable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['password'];

    /**
     * The attributes that are hidden.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
        'activated',
        'token',
        'confirmation_key',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'creation_time',
        'expire_time'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'domain',
        'email',
        'activated',
        'token',
        'signup_ip_address',
        'signup_confirmation_ip_address',
        'signup_sm_ip_address',
        'admin_ip_address',
        'updated_ip_address',
        'deleted_ip_address',
        'confirmation_key',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                                => 'integer',
        'username'                          => 'string',
        'first_name'                        => 'string',
        'last_name'                         => 'string',
        'domain'                            => 'string',
        'email'                             => 'string',
        'activated'                         => 'boolean',
        'token'                             => 'string',
        'signup_ip_address'                 => 'string',
        'signup_confirmation_ip_address'    => 'string',
        'signup_sm_ip_address'              => 'string',
        'admin_ip_address'                  => 'string',
        'updated_ip_address'                => 'string',
        'deleted_ip_address'                => 'string',
        'confirmation_key'                  => 'string',
        'ip_address'                        => 'string',
        'user_agent'                        => 'string',
        'creation_time'                     => 'datetime',
        'expire_time'                       => 'datetime',
    ];

    /**
     * Get the socials for the user.
     */
    public function social()
    {
        return $this->hasMany('App\Models\Social');
    }

    /**
     * Get the passwords for the account.
     */
    public function password()
    {
        return $this->hasOne('App\Models\Password');
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    /**
     * The profiles that belong to the user.
     */
    public function profiles()
    {
        return $this->belongsToMany('App\Models\Profile')->withTimestamps();
    }

    /**
     * Check if a user has a profile.
     *
     * @param  string  $username
     *
     * @return bool
     */
    public function hasProfile($username)
    {
        foreach ($this->profiles as $profile) {
            if ($profile->account->username === $username) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add/Attach a profile to a user.
     *
     * @param  Profile $profile
     */
    public function assignProfile(Profile $profile)
    {
        return $this->profiles()->attach($profile);
    }

    /**
     * Remove/Detach a profile to a user.
     *
     * @param  Profile $profile
     */
    public function removeProfile(Profile $profile)
    {
        return $this->profiles()->detach($profile);
    }
}
