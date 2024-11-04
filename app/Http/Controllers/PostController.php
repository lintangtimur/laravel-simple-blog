<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    
    /**
     * Display a paginated list of posts ordered by publish date.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $posts = Post::published()->orderBy('publish_date', 'desc')->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $validated['is_draft'] = isset($validated['is_draft']) ? 1 : 0;
            $validated['user_id'] = auth()->user()->id;

            Post::create($validated);

            $msg = ['status' => 1, 'msg' => 'Post created successfully'];

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to create post', ['error' => $th->getMessage()]);
            $msg = ['status' => 0, 'msg' => $th->getMessage()];
        }

        return redirect()->route('posts.create')->with(['msg' => $msg]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        try {
            DB::beginTransaction();
            $validated = $request->validated();
            $validated['is_draft'] = isset($validated['is_draft']) ? 1 : 0;

            $post->update($validated); 

            $msg = ['status' => 1, 'msg' => 'Post updated successfully'];

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to create post', ['error' => $th->getMessage()]);
            $msg = ['status' => 0, 'msg' => $th->getMessage()];
        }

        return redirect()->route('posts.edit', $post)->with(['msg' => $msg]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $post->delete();

            $msg = ['status' => 1, 'msg' => 'Post deleted successfully'];

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to create post', ['error' => $th->getMessage()]);
            $msg = ['status' => 0, 'msg' => $th->getMessage()];
        }

        return redirect()->route('welcome')->with(['msg' => $msg]);
    }
}
