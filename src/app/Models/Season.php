<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * この季節に属する商品を取得します。（多対多リレーション）
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
