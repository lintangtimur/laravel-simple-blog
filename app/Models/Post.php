<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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

    public static function store($validated)
    {
        return Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'publish_date' => $validated['published_at'],
            'user_id' => Auth::user()->id,
            'is_draft' => isset($validated['is_draft']) ? 1 : 0
        ]);
    }


    public static function edit($validated, $id)
    {
        return Post::where('id', $id)->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'publish_date' => $validated['published_at'],
            'user_id' => Auth::user()->id,
            'is_draft' => isset($validated['is_draft']) ? 1 : 0
        ]);
    }

    public function scopePublished(Builder $query)
    {
        $query->where('is_draft', 0)->where('publish_date', '<=', now());
    }
}
