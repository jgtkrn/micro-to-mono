<?php

namespace App\Models\v2\Users;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CanResetPassword, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'team_id',
        'employment_status',
        'nickname',
        'staff_number',
        'email_cityu',
        'user_status',
        'access_role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_status' => 'boolean',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function teamList()
    {
        return $this->belongsToMany('teams', 'id', 'name', 'user_id')->pivot('id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    public function accessRoles()
    {
        return $this->belongsTo(AccessRole::class, 'access_role_id');
    }

    public function getIsAdminAttribute()
    {
        $access_role = $this->accessRoles != null ? $this->accessRoles->name : null;

        return $access_role == 'admin';
    }

    public function getIsManagerAttribute()
    {
        $access_role = $this->accessRoles != null ? $this->accessRoles->name : null;

        return $access_role == 'manager';
    }

    public function getIsUserAttribute()
    {
        $access_role = $this->accessRoles != null ? $this->accessRoles->name : null;

        return $access_role == 'user';
    }

    public function getIsHelperAttribute()
    {
        $access_role = $this->accessRoles != null ? $this->accessRoles->name : null;

        return $access_role == 'helper';
    }

    public function routeNotificationForMail()
    {
        return $this->email_cityu;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
