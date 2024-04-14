<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Annotations as OA;
use App\Notifications\ResetPasswordNotification;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *
 *     @OA\Property(
 *          property="id",
 *          type="integer",
 *          example="1"
 *     ),
 *
 *     @OA\Property(
 *          property="name",
 *          type="string",
 *          example="John Snow"
 *     ),
 *
 *     @OA\Property(
 *          property="nickname",
 *          type="string",
 *          example="john"
 *     ),
 *
 *     @OA\Property(
 *          property="staff_number",
 *          type="string",
 *          example="staff0001"
 *     ),
 * 
 *     @OA\Property(
 *          property="phone_number",
 *          type="string",
 *          example="08977888"
 *     ),
 *
 *     @OA\Property(
 *          property="email",
 *          type="string",
 *          format="email",
 *          example="snow.john@stark.com"
 *     ),
 *
 *     @OA\Property(
 *          property="email_cityu",
 *          type="string",
 *          format="email",
 *          example="snow.john@cityu.edu.hk"
 *     ),
 *
 *     @OA\Property(
 *          property="email_verified_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 *
 *     @OA\Property(
 *          property="teams",
 *          type="array",
 *          example={1,2,3,4},
 *          @OA\Items(type="integer", example=1)
 *     ),
 *
 *     @OA\Property(
 *          property="roles",
 *          type="array",
 *          example={1,2,3,4},
 *          @OA\Items(type="integer", example=1)
 *     ),
 *
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     ),
 *
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          format="date-time",
 *          example="2022-05-13T00:00:00Z"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

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
        'access_role_id'
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
        'user_status' => 'boolean'
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

    public function access_roles()
    {
        return $this->belongsTo(AccessRole::class, 'access_role_id');
    }

    public function user_capacitor()
    {
        return $this->hasOne(UserCapacitor::class);
    }

    public function getIsAdminAttribute()
    {
        $access_role = $this->access_roles != null ? $this->access_roles->name : null;
        return $access_role == 'admin';
    }

    public function getIsManagerAttribute()
    {
        $access_role = $this->access_roles != null ? $this->access_roles->name : null;
        return $access_role == 'manager';
    }

    public function getIsUserAttribute()
    {
        $access_role = $this->access_roles != null ? $this->access_roles->name : null;
        return $access_role == 'user';
    }

    public function getIsHelperAttribute()
    {
        $access_role = $this->access_roles != null ? $this->access_roles->name : null;
        return $access_role == 'helper';
    }

    public function routeNotificationForMail(){
        return $this->email_cityu;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
