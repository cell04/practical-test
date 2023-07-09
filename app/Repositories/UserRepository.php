<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository
{

    public function __construct(protected User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function store($request)
    {
        // $path = $request->image->store('public/images');
        $user = $this->user->create($request->all());

        // return $user->image()->create(['file_path' => $path]);

        return $user;
    }

    public function changePassword($request)
    {
        if ($request->has('current_password')) {
            if (password_verify($request->current_password, auth()->user()->password)) {
                if ($request->has('user_id')) {
                    $user = $this->user->findOrFail($request->user_id);
                } else {
                    $user = $this->user->findOrFail(auth()->user()->id);
                }
                return $user->update(['password' => Hash::make($request->new_password)]);
            } else {
                abort(422, 'Password Not Found!');
            }
        }

        if ($request->has('user_id')) {
            $user = $this->user->findOrFail($request->user_id);
        } else {
            $user = $this->user->findOrFail(auth()->user()->id);
        }
        
        return $user->update(['password' => Hash::make($request->new_password)]);
    }

    public function following($request, $id)
    {
        if ($request->following_id == $id) {
            abort(422, 'You cannot follow yourself');
        }

        $user = $this->user->findOrFail($id);

        return $user->following()->attach($request->following_id);
    }

    public function unfollow($request, $id)
    {
        if ($request->following_id == $id) {
            abort(422, 'You cannot unfollow yourself');
        }

        $user = $this->user->findOrFail($id);

        return $user->following()->detach($request->following_id);
    }

    public function followed($id)
    {
        $user = $this->user->findOrFail($id);

        return $user->following; 
    }

    public function followers($id)
    {
        $user = $this->user->findOrFail($id);

        return $user->followers; 
    }

    public function suggestedFollowing()
    {
        $collection = collect($this->all());
        $sorted = $collection->sortBy([['followers_count', 'desc'], ['created_at', 'desc']]);

        return $sorted->values()->all();
    }
}
