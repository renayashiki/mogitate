<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'image',
    ];

    /**
     * 商品が属する季節を（多対多リレーション）
     */
    public function seasons(): BelongsToMany
    {

        return $this->belongsToMany(Season::class);
    }
}

