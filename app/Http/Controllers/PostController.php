<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            Post::store($validated);

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
    public function show(string $id)
    {
        $post = Post::findOrFail($id);

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        $validated = $request->validated();

        // Protect route from unauthorized users
        $post = Post::findOrFail($id);
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            Post::edit($validated, $id);

            $msg = ['status' => 1, 'msg' => 'Post updated successfully'];

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Failed to create post', ['error' => $th->getMessage()]);
            $msg = ['status' => 0, 'msg' => $th->getMessage()];
        }

        return redirect()->route('posts.create')->with(['msg' => $msg]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Protect route from unauthorized users
        $post = Post::findOrFail($id);
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            Post::where('id', $id)->delete();

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
