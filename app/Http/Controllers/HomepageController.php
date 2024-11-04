<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomepageController extends Controller
{
    public function index()
    {
        $posts = Post::published()->get();

        return view('welcome', compact('posts'));
    }
}
