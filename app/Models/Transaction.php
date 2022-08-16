<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\UserRoleEnum;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'product_id',
        'amount',
        'tax',
        'admin_fee',
        'total',
        'deleted_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->setAttribute('uuid', Str::uuid()->toString());
        });
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            set: fn () => Carbon::now(),
        );
    }

    protected function updateddAt(): Attribute
    {
        return Attribute::make(
            set: fn () => Carbon::now(),
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeFilterUserData($query)
    {
        if (auth()->user()->role == UserRoleEnum::CUSTOMER) {
            return $query->where('user_id', auth()->user()->id);
        }
        return $query;
    }

    public function scopeLimitProduct($query, $limit_query_param)
    {
        return $query->when(
                        $limit_query_param, 
                        function($query, $limit_query_param) {
                            return $query->limit($limit_query_param);
                        }
                    );
    }

    public function scopeSortProduct($query, $sortby_query_param)
    {
        return $query->when(
                        $sortby_query_param, 
                        function($query, $sortby_query_param) {
                            return $query->where('product_id', $sortby_query_param);
                        }
                    );
    }

    public function scopeOrderProduct($query, $orderby_query_param)
    {
        return $query->when(
                        $orderby_query_param, 
                        function($query, $orderby_query_param) {
                            return $query->orderBy($orderby_query_param);
                        }
                    );
    }
}
