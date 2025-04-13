<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $fillable = [
    'post_id',
    'customer_id',
    'parent_id',
    'content',
    'is_visible',
    'is_approved',
    'approved_by',
  ];

  public function post()
  {
    return $this->belongsTo(Post::class);
  }

  public function customer()
  {
    return $this->belongsTo(User::class, 'customer_id');
  }

  public function approver()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function parent()
  {
    return $this->belongsTo(PostComment::class, 'parent_id');
  }
}
