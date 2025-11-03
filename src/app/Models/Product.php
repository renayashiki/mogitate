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
     * 商品が属する季節を取得します。（多対多リレーション）
     */
    public function seasons(): BelongsToMany
    {
        // 中間テーブル名 'product_season'
        return $this->belongsToMany(Season::class);
    }
}

