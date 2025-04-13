<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $fillable = [
    'title',
    'slug',
    'content',
    'created_by',
    'category_id',
    'published_at',
    'image',
    'status',
  ];

  public function category()
  {
    return $this->belongsTo(PostCategory::class);
  }

  public function auther()
  {
    return $this->belongsTo(User::class, 'created_by','id');
  }

  public function tags()
  {
    return $this->belongsToMany(PostTag::class, 'post_tag', 'post_id', 'tag_id');
  }

  public function comments()
  {
    return $this->hasMany(PostComment::class);
  }
}
