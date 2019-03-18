<?php

namespace App;

use App\Purchase;
use App\UserGroup;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $primaryKey = 'id_user';

    const BLOCKED = 1;
    const UNBLOCKED = 0;

    const OPERATOR_USER = 1;
    const MANAGER_USER = 2;
    const REGULAR_USER = 3;

    protected $fillable = [
        'id_group',
        'name', 
        'email', 
        'gender',
        'birthdate',
        'address',
        'nationality',
        'city',
        'postcode',
        'phone',
        'password',
        'pp',
        'blocked'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function setNameAttribute($name) {
        $this->attributes['name'] = strtolower($name);
    }

    public function getNameAttribute($name) {
        return ucwords($name);
    }

    public function setEmailAttribute($email) {
        $this->attributes['email'] = strtolower($email);
    }

    public function isBlocked() {
        return $this->blocked == User::BLOCKED;
    }

    public function isOperator() {
        return $this->id_group == User::OPERATOR_USER;
    }

    public function permission() {
        return $this->belongsTo(UserGroup::class, 'id_group');
    }

    public function purchase() {
        return $this->hasMany(Purchase::class, 'id_user');
    }

    public static function generatePassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
