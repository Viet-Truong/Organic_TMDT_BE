<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = ['shop_id','name', 'price', 'description', 'category_id'];

    public function Images()
    {
        return $this->hasMany(Image::class, 'image_id','image_id');
    }

    public function Users()
    {
        return $this->belongsTo(User::class, 'shop_id','id');
    }

    public function Categories()
    {
        return $this->belongsTo(category::class, 'category_id','id');
    }


}
