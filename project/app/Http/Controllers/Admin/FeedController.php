<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Traits\Controlling;

class FeedController extends Controller
{
    use Controlling;

    protected $model = Feed::class;

    protected $listView = 'admin.feeds.list';
    protected ?string $viewPermission = 'view-user';

    protected $searching = ['body', 'type', 'privacy', 'user:name', 'user:email'];

    public function list()
    {
        goIfUserCan('view-user');

        return $this->data('Feeds');
    }

    public function details($id)
    {
        goIfUserCan('view-user');

        $title = 'Feed Details';

        $feed = Feed::with([
            'user:id,name,email,image',
            'likedByUsers:id,name,email,image',
        ])->findOrFail($id);

        return view('admin.feeds.details', compact('title', 'feed'));
    }

    protected function listQuery($query)
    {
        return $query->with([
            'user:id,name,email,image',
            'likedByUsers:id,name,email,image',
        ])->withCount('likedByUsers');
    }
}
