<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Validator;

class PostController extends Controller
{
    public function list()
    {
        $posts = Post::whereDate('created_at', '=', date('Y-m-d'))->get();

        $data = collect();
        foreach ($posts as $post) {
            $data->add([
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'tags' => $post->tags,
                'like_counts' => $post->likes->count(),
                'created_at' => $post->created_at,
            ]);
        }

        return apiResponse('data', $data, 200);
    }

    public function toggleReaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|int|exists:posts,id',
            'like' => 'required|boolean',
         ]);

        if ($validator->fails()) {
            return apiResponse('Validation Error', $validator->errors()->all(), 422);

        }else{

            $post = Post::find($request->post_id);
            if (! $post) {
                return apiResponse('Message', 'Model not Found', 404);

            }
            if ($post->user_id == auth()->id()) {

                return apiResponse('Message', 'You cannot like your post', 500);
            }

             $like = Like::where('post_id', $request->post_id)
                            ->where('user_id', auth()->id())
                            ->first();

            if ($like) {

                if ($like->post_id == $request->post_id) {
                    
                    if ($request->like) {
                        return apiResponse('Message', 'You already liked this post', 500);
                    }else{
                        $like->delete();
                        return apiResponse('Message', 'You unlike this post successfully', 200); 
                    }
                }else{
                    return apiResponse('Message', 'Model not found', 500);
                }
                
            }else{
                Like::create([
                    'post_id' => $request->post_id,
                    'user_id' => auth()->id(),
                ]);
                return apiResponse('Message', 'You like this post successfully', 200);
            }
        }
    }
}
