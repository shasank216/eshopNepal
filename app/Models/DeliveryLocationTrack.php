<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $seller_id
 * @property string $f_name
 * @property string $l_name
 * @property string $address
 * @property string $email
 * @property string $country_code
 * @property string $phone
 * @property string $identity_type
 * @property string $identity_number
 * @property string $identity_image
 * @property int $is_active
 * @property int $is_online
 * @property string $password
 * @property string $auth_token
 * @property string $fcm_token
 * @property string $app_language
 */
class DeliveryLocationTrack extends Model
{
    protected $hidden = ['password','auth_token'];

    protected $table = 'deliveryman_locations';

    protected $fillable = [
        'delivery_man_id',
        'order_id',
        'latitude',
        'longitude',
    ];

    public function delivery_man():HasOne
    {
        return $this->hasOne(DeliveryMan::class,'id','delivery_man_id');
    }
    public function orders():HasMany
    {
        return $this->hasMany(Order::class,'delivery_man_id');
    }
    public function deliveredOrders():HasMany
    {
        return $this->hasMany(Order::class,'delivery_man_id')->where('order_status','delivered');
    }

    public function wallet():HasOne
    {
        return $this->hasOne(DeliverymanWallet::class);
    }

    public function transactions():HasMany
    {
        return $this->hasMany(DeliveryManTransaction::class);
    }
    public function chats():HasMany
    {
        return $this->hasMany(Chatting::class);
    }
    public function review():HasMany
    {
        return $this->hasMany(Review::class, 'delivery_man_id');
    }

    public function rating():HasMany
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, delivery_man_id'))
            ->groupBy('delivery_man_id');
    }
}
