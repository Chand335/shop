<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $fillable = [
    'name',
    'slug',
    'description',
    'is_visible',
  ];

  public function posts()
  {
    return $this->hasMany(Post::class,'category_id');
  }
}
