<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use App\Models\FeedLike;

class FeedLikeController extends Controller {
    public function like($id) {
        $feed = Feed::with(['user:id,name,image', 'likedByUsers:id,name,image'])->findOrFail($id);

        if ($feed->privacy !== 'public' && $feed->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'note'    => 'feed_not_accessible',
                'message' => 'This post is not accessible to you.',
            ]);
        }


        $like = FeedLike::where('feed_id', $feed->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$like) {
            FeedLike::create([
                'feed_id' => $feed->id,
                'user_id' => auth()->id(),
            ]);

            $feed->increment('likes_count');
        }

        $feed->refresh()->load(['user:id,name,image', 'likedByUsers:id,name,image']);

        return response()->json([
            'status'  => 'success',
            'note'    => 'feed_liked',
            'message' => 'Feed liked successfully',
            'feed'    => $feed,
        ]);
    }

    public function unlike($id) {
        $feed = Feed::with(['user:id,name,image', 'likedByUsers:id,name,image'])->findOrFail($id);


        if ($feed->privacy !== 'public' && $feed->user_id !== auth()->id()) {
            return response()->json([
                'status'  => 'error',
                'note'    => 'feed_not_accessible',
                'message' => 'This post is not accessible to you.',
            ]);
        }


        $like = FeedLike::where('feed_id', $feed->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete();

            if ($feed->likes_count > 0) {
                $feed->decrement('likes_count');
            }
        }

        $feed->refresh()->load(['user:id,name,image', 'likedByUsers:id,name,image']);

        return response()->json([
            'status'  => 'success',
            'note'    => 'feed_unliked',
            'message' => 'Feed unliked successfully',
            'feed'    => $feed,
        ]);
    }
}
