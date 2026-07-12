<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Traits\Controlling;

class BlogCategoryController extends Controller
{
    use Controlling;

    protected $model = BlogCategory::class;
    protected $listView = 'admin.blog_categories.list';
    protected ?string $viewPermission = 'view-blog-categories';
    protected ?string $createPermission = 'save-blog-categories';
    protected ?string $updatePermission = 'save-blog-categories';

    public function status($id)
    {
        goIfUserCan('save-blog-categories');

        $category = $this->model::findOrFail($id);
        $category->status = $category->status ? 0 : 1;
        $category->save();

        return back()->withSuccess(__('Blog category status changed'));
    }

    public function list()
    {
        return $this->data(__('Blog Categories'));
    }


    protected function listQuery($query)
    {
        return $query->searching(['name'])->withCount('blogs')->latest();
    }


    public function save(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|unique:blog_categories,name,' . $id,
        ];

        $this->dataSave($id, $rules);

        return back()->withSuccess(__('Blog category saved successfully'));
    } 
    
}
