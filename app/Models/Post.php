<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    # A post belongs to a user
    # Use this method to get the owner of the post
    public function user(){
        return $this->belongsTo(User::class)->withTrashed();
    }

    # Use this method to get all the categories under
    # a specific post
    public function categoryPost(){
        return $this->hasMany(CategoryPost::class);
    }

    # Use this method to get all the comments of a post
    public function comments(){
        return $this->hasMany(Comment::class);
    }

    # One to many relationship
    # A post may contain many likes
    # Use this method to get the likes of a post
    public function likes(){
        return $this->hasMany(Like::class);
    }

    # Check if the post is already liked
    # Return true if the post is liked, else return false
    public function isLiked(){ // true or false
        return $this->likes()->where('user_id', Auth::user()->id)->exists();
        // If the ID of the AUTH user exists in the likes table, then
        // that confirms that that user already like the post
    }

}
