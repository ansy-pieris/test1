<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'category_id','name','slug','description','price','stock','image','added_by','is_active','is_featured'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()   { return $this->belongsTo(Category::class, 'category_id', 'category_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'added_by'); }
    public function cartItems()  { return $this->hasMany(CartItem::class, 'product_id', 'product_id'); }
    public function orderItems() { return $this->hasMany(OrderItem::class, 'product_id', 'product_id'); }

    // Route model binding by slug
    public function getRouteKeyName() { return 'slug'; }

    // Auto-generate unique slug on create (immutable by default)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                // product_id isn't assigned yet, so start with name
                $model->slug = static::uniqueSlug(Str::slug($model->name));
            }
        });
    }

    protected static function uniqueSlug($base)
    {
        $slug = $base; $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
