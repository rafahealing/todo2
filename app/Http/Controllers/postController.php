<?php

namespace App\Http\Controllers;


// import model "post"
use App\Models\post;
use App\Models\post as ModelsPost;
use Illuminate\Contracts\view\view;

// return type view
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;


class postController extends Controller
{
    /**
     * index
     * 
     * @return View
     */
    public function index(): View
    {
        // get posts
        $posts = post::latest()->paginate(5);

        // render view with posts
        return view('/posts.index', compact('posts'));
    }


    public function create():View
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // validasi form
        $this->validate($request ,[
            'image'  => 'required|image|mimes:jpg,png|max:2048',
            'title'  => 'required|min:5',
            'content'=> 'required|min:10',
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('pulic/posts', $image->hashName());

        // create data
        post::create([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content
        ]);

        return redirect()->route('posts.index')->with(['success' => 'data saved |']);
    }

    public function show(string $id): View
    {
        $post = Post::findOrFail($id);


        return view('posts.show', compact('post'));
    }

    public function edit(string $id): View
    {
        $post =  Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }
    

    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $this->validate($request ,[
            'image'  => 'image|mimes:jpg,png|max:2048',
            'title'  => 'required|min:5',
            'content'=> 'required|min:10',
        ]);

        $post = Post::findOrfail($id);

        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $image->storeAs('public/posts/', $image->hashName());

            Storage::delete('public/posts/'.$post->image);

            $post->update([
                'image'   => $image->hashName(),
                'title'   => $request->title,
                'content'   => $request->content
            ]);

        } else {

            $post->update([
                'title'   => $request->title,
                'content'   => $request->content
            ]);
        }

        return redirect()->route('posts.index')->with(['success'=> 'Data updated']);
    }


    public function destroy($id): RedirectResponse

    {
        $post = Post::findOrFail($id);

        storage::delete('public/posts/', $post->image);

        $post->delete();

        return redirect()->route('posts.index')->with(['success'=> 'Data Deleted']);
    }


}


