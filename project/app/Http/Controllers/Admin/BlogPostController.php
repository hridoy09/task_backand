<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function list()
    {
        goIfUserCan('view-blog-posts');

        $title = 'Blog Posts';

        $data = BlogPost::latest()->paginate();

        return view('admin.blog_post.list', compact('title', 'data'));
    }

    public function catagorised($categoryId)
    {
        goIfUserCan('view-blog-posts');

        $title = 'Blog Posts';

        $data = BlogPost::latest()->where('category_id', $categoryId)->paginate();

        return view('admin.blog_post.list', compact('title', 'data'));
    }

    public function published()
    {
        goIfUserCan('view-blog-posts');

        $title = __('Published Posts');

        $data = BlogPost::published()->latest()->paginate();

        return view('admin.blog_post.list', compact('title', 'data'));
    }

    public function unpublished()
    {
        goIfUserCan('view-blog-posts');

        $title = __('Unpublished Posts');

        $data = BlogPost::published()->latest()->paginate();

        return view('admin.blog_post.list', compact('title', 'data'));
    }



    public function create()
    {
        goIfUserCan('save-blog-posts');

        $title = __('Add New Blog Post');

        $categories = BlogCategory::active()->get();

        return view('admin.blog_post.form', compact('title', 'categories'));
    }

    public function save(Request $request, $id = null)
    {
        goIfUserCan('save-blog-posts');

      
        $request->validate([
            'title'                        => 'required|string|max:255|unique:blog_posts,title,' . $id,
            'image'                        => $id ? 'nullable|image|max:4096' : 'nullable|image|max:4096',
            'body'                         => 'required|string',
            'seo_content.meta_title'       => 'nullable|string|max:255',
            'seo_content.meta_description' => 'nullable|string|max:500',
            'seo_content.meta_keywords'    => 'nullable|string|max:255',
            'status'                       => 'required|in:0,1'
        ]);

        $category = BlogCategory::active()->findOrFail($request->category_id);

        $post              = $id ? BlogPost::findOrFail($id) : new BlogPost();
        $post->title       = $request->title;
        $post->slug        = str($request->title)->slug();
        $post->body        = $request->body;
        $post->seo_content = (object) ($request->seo_content ?? []);
        $post->status      = $request->status;
        $post->category_id = $category->id;

        if ($request->hasFile('image')) {
            $path =   FileManager::uploadToAssets($request->file('image'), 'assets/images/blogs', $post->image);
            $post->image = $path;
        }

        $post->admin_id = admin()->id;
        $post->save();

        $message = $id ? __('Post updated successfully.') : __('Post created successfully.');

        return to_route('admin.blog.list')->withSuccess($message);
    }

    public function edit($id)
    {
        goIfUserCan('save-blog-posts');

        $title = __('Edit Blog Post');

        $blogPost = BlogPost::findOrFail($id);
        $categories = BlogCategory::active()->get();

        return view('admin.blog_post.form', compact('title', 'blogPost', 'categories'));
    }

    public function delete($id)
    {
        goIfUserCan('delete-blog-posts');

        $post = BlogPost::findOrFail($id);

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->withSuccess(__('Blog Post Deleted'));
    }
}
