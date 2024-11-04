<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    /**
     * Display a listing of the posts by authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Auth::user()?->posts()->paginate(3);
        
        return view('welcome', compact('posts'));
    }
}
