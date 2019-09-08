<?php

namespace App\Models;

use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $email_verified_at
 * @property string|null $remember_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @property string|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatus($value)
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    public static function generatePassword($length = 32)
    {
        return bcrypt(Str::random($length));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getExecutorNameByStatus($status)
    {
        if ($status === 'modeling'){
            $index = 0;
        } elseif ($status === 'printing'){
            $index = 1;
        }elseif ($status === 'painting'){
            $index = 2;
        }elseif ($status === '3dscanning'){
            $index = 3;
        }elseif ($status === 'photographer'){
            $index = 4;
        }elseif ($status === 'photoshop'){
            $index = 5;
        } else {
            return collect([]);
        }
        $executors = self::all();
        $order_executors = [];
        foreach($executors as $key => $executor){
            if((int)$executor->status[$index] === 1 && (int)$executor->status[8] === 0 ){
                $order_executors[$key] = [
                    'id' => $executor->id ,
                    'name' => $executor->name,
                ];
            }
        }
        return collect($order_executors);


    }
}
