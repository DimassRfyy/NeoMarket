<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id']; 
    protected $fillable = ['id','name','slug','cover','price','about','path_file','category_id','creator_id'];

    public function category(){
        return $this->belongsTo(Category::class,'category_id'); //akses category table
    }

    public function creator(){
        return $this->belongsTo(User::class); //check kepemilikan 
    }

}
