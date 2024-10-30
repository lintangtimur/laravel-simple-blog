<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;

use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('posts.index' , compact('posts'));
    }

    public function createForm()
    {
        return view('posts.create');
    }

    public function store(PostRequest $req)
    {
        $val = $req->validated();
        
        // dd($val, $val['title']);
        DB::beginTransaction();
            Post::create([
                'title' => $val['title'],
                'content' => $val['content'],
                'publish_date' => $val['published_at'],
                'is_published' => isset($val['is_published']) ? $val['is_published'] : 0,
            ]);
            DB::commit();
            
            return redirect()->route('posts.create');
        


    }

    public function show($id){

    }

    public function edit()
    {

    }

    public function delete()
    {

    }
}
