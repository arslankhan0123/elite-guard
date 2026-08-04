<?php

namespace App\Repositories;

use App\Models\PostEsc;
use Illuminate\Support\Facades\Storage;

class PostEscRepository
{
    /**
     * Get all Post & ESC records.
     */
    public function getPostEsc(): array
    {
        $posts = PostEsc::orderBy('date', 'desc')->get()->map(function (PostEsc $post) {
            $post->setAttribute(
                'pdf_url',
                $post->pdf_path ? Storage::disk('public')->url($post->pdf_path) : null
            );

            return $post;
        });

        return [
            'status' => true,
            'message' => 'Post & ESC data fetched successfully.',
            'posts' => $posts,
        ];
    }
}
