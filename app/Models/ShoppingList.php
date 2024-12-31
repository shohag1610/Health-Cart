<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $table = 'shopping_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'item_name',
        'item_price',
        'is_purchased',
        'order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
