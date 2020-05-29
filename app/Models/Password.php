<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Password extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'passwords';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that are hidden.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'password',
        'algorithm',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                                => 'integer',
        'account_id'                        => 'integer',
        'password'                          => 'string',
        'algorithm'                         => 'string',
    ];

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    /* public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = hash('sha256', $password->account->username.':'.$password->account->domain.':'.$password);
    }  */
}
