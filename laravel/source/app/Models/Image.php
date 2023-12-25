<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'images';
    protected $primaryKey = 'image_id';
    protected $fillable = ['url', 'product_id'];

    public function Product()
    {
        return $this->belongsTo(Product::class,'product_id','product_id');
    }
}
