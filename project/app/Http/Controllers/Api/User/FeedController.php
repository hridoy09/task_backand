<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Services\FileManager;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $feeds = Feed::with('user:id,name,image')
            ->visibleTo(auth()->id())
            ->latest()
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'note'   => 'feed_list',
            'feeds'  => $feeds,
        ]);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'type'       => 'required|in:text,photo,video,event,article',
            'body'       => 'nullable|string',
            'media'      => 'nullable|file|max:51200',
            'event_date' => 'nullable|required_if:type,event|date',
            'privacy'    => 'nullable|in:public,friends,private',
        ]);

        $feed             = new Feed();
        $feed->user_id    = auth()->id();
        $feed->type       = $request->type;
        $feed->body       = $request->body;
        $feed->event_date = $request->event_date;
        $feed->privacy    = $request->privacy ?? 'public';

        if ($request->hasFile('media')) {
            $file              = $request->file('media');
            $feed->media_type  = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
            $feed->media_path  = FileManager::uploadToAssets($file, 'assets/uploads/feeds');
        }

        $feed->save();

        return response()->json([
            'status'  => 'success',
            'note'    => 'feed_created',
            'message' => 'Post created successfully',
            'feed'    => $feed->load('user:id,name,image'),
        ]);
    }

    public function show($id)
    {
        $feed = Feed::with('user:id,name,image')->findOrFail($id);

        abort_if($feed->privacy !== 'public' && $feed->user_id !== auth()->id(), 403, 'This post is not accessible to you.');

        return response()->json([
            'status' => 'success',
            'note'   => 'feed_details',
            'feed'   => $feed,
        ]);
    }

    public function update(Request $request, $id)
    {
        $feed = Feed::findOrFail($id);

        abort_if($feed->user_id !== auth()->id(), 403, 'You are not allowed to edit this post.');

        $request->validate([
            'type'       => 'sometimes|required|in:text,photo,video,event,article',
            'body'       => 'nullable|string',
            'media'      => 'nullable|file|max:51200',
            'link'       => 'nullable|url',
            'event_date' => 'nullable|date',
            'privacy'    => 'nullable|in:public,friends,private',
        ]);

        $feed->fill($request->only(['type', 'body', 'link', 'event_date', 'privacy']));

        if ($request->hasFile('media')) {
            $file              = $request->file('media');
            $feed->media_type  = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
            $feed->media_path  = FileManager::uploadToAssets($file, 'assets/uploads/feeds', $feed->media_path);
        }

        $feed->save();

        return response()->json([
            'status'  => 'success',
            'note'    => 'feed_updated',
            'message' => 'Post updated successfully',
            'feed'    => $feed->load('user:id,name,image'),
        ]);
    }

    public function destroy($id)
    {
        $feed = Feed::findOrFail($id);

        abort_if($feed->user_id !== auth()->id(), 403, 'You are not allowed to delete this post.');

        if ($feed->media_path && file_exists(public_path($feed->media_path))) {
            unlink(public_path($feed->media_path));
        }

        $feed->delete();

        return response()->json([
            'status'  => 'success',
            'note'    => 'feed_deleted',
            'message' => 'Post deleted successfully',
        ]);
    }
}
