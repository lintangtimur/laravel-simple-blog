<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ["id"];
    protected $table = "posts";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublished(Builder $query)
    {
        $query->where('is_draft', 0)->where('publish_date', '<=', now());
    }
}
