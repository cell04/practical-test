<?php

namespace App\Repositories;

use App\Models\Tweet;
use App\Models\User;
use App\Repositories\Repository;
use File;

class TweetRepository extends Repository
{

    public function __construct(protected Tweet $tweet, protected User $user)
    {
        parent::__construct($tweet);
        $this->tweet = $tweet;
        $this->user = $user;
    }

    public function store($request)
    {
        $path = $request->image->store('public/tweets');
        $path = str_replace('public', env('APP_URL').'/storage', $path);
        $tweet = $this->tweet->create($request->all());
        $tweet->images()->create(['file_path' => $path]);

        return $tweet;
    }

    public function storeImage($request, $id)
    {
        $path = $request->image->store('public/tweets');
        $path = str_replace('public', env('APP_URL').'/storage', $path);
        $tweet = $this->tweet->findOrFail($id);
            
        return $tweet->images()->create(['file_path' => $path]);
    }

    public function deleteImage($id, $image)
    {
        $tweet = $this->tweet->findOrFail($id);
        $image = $tweet->images()->findOrFail($image);
        
        $image_path = public_path($image->file_path);
        
        if(File::exists($image_path)) {
            File::delete($image_path);
        }
        return $image->delete();
    }

    public function findOrFail($id)
    {
        return $this->tweet->with('images')->findOrFail($id);
    }

    public function destroy($id)
    {
        $tweet = $this->tweet->with('images')->findOrFail($id);

        if ($tweet->images) {
            foreach ($tweet->images as $image) {
                $image_path = public_path($image->file_path);
        
                if(File::exists($image_path)) {
                    File::delete($image_path);
                }
                
                $image->delete();
            }
        }

        return $this->model->destroy($id);
    }
    
    public function followedTweets(
        $request = null,
        $length = 15,
        $orderBy = 'desc',
        $removePage = true
    ) {
        if ($orderBy == null) {
            $orderBy = 'desc';
        }

        $usersCollection = collect(auth()->user()->following);
        $usersId = $usersCollection->pluck('id')->toArray();

        return $this->tweet->whereIn('user_id', $usersId)
            ->filter($request)
            ->orderBy('created_at', $orderBy)
            ->paginate($length)
            ->withPath(
                $this->model->createPaginationUrl($request, $removePage)
            );
    }
}
