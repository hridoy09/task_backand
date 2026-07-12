<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\PageView;

class SiteController extends Controller
{
    public function contact()
    {
        $title = __('Contact Us');

        count_page_view('/contact');

        return theme('contact', compact('title'));
    }

    public function blogs()
    {
        $title = __('Blog Posts');

        count_page_view('/blog');

        $blogPosts = BlogPost::published()->paginate();

        return theme('blogs', compact('title', 'blogPosts'));
    }

    public function blogDetails($slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        $title = $post->title;

        $seoContent = get_seo_content($slug, true);

        return theme('blog_details', compact('title', 'post', 'seoContent'));
    }

    public function changeLang($lang)
    {
        session(['locale' => $lang]);

        return back()->withSuccess(__('Language changed successfully'));
    }

    public function home()
    {
        $title = 'Home';

        count_page_view();

        $seoContent = get_seo_content('home');

        return theme('home', compact('title', 'seoContent'));
    }

    public function renderPageBySlug($pageSlug)
    {

        $page = Page::where('slug', $pageSlug)->firstOrFail();

        count_page_view($pageSlug);

        $title = $page->title;

        $seoContent = get_seo_content($pageSlug);

        $sections = $page->sections;

        return theme('page', compact('title', 'sections', 'page', 'seoContent'));
    }
}