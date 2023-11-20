<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Like;

class LikeController extends Controller
{
    private $like;

    public function __construct(Like $like){
        $this->like = $like;
    }

    public function store($post_id){
        $this->like->user_id = Auth::user()->id; // user who liked the post
        $this->like->post_id = $post_id;         // post being liked
        $this->like->save();                     // execute the query

        return redirect()->back();               // return to the previous page
    }

    # Unlike/ Destroy
    # This method will destroy/or remove the likes details in the likes table
    public function destroy($post_id){
        $this->like
            ->where('user_id', Auth::user()->id) // user who liked the post
            ->where('post_id', $post_id)         // post being liked
            ->delete();                          // delete the user id and post id

        return redirect()->back();
    }
}
