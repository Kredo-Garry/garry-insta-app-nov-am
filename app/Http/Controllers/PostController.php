<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;     //inherit the post model
use App\Models\Category; //inherit the category model

class PostController extends Controller
{
    private $post;
    private $category;

    public function __construct(Post $post, Category $category){
        $this->post = $post;
        $this->category = $category;
    }

    # This method is going to retrieve all the categories in categories table
    # and display the categories into the create.blade.php
    public function create(){
        $all_categories = $this->category->all();
        //The same as "SELECT * FROM categories"

        return view('users.posts.create')->with('all_categories', $all_categories);
    }

    # This method is use to insert post details into posts table
    public function store(Request $request){
        # 1. Validate the first
        $request->validate([
            'category'      => 'required|array|between:1,3',
            'description'   => 'required|min:1|max:1000',
            'image'         => 'required|mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        # 2. prepare the view (create.blade.php) so that it will properly display the error message when the validation encounters a message

        # 3. Save the post into the database table
        $this->post->user_id   = Auth::user()->id; //John Smith --> id of 1
        $this->post->image     = 'data:image/' . $request->image->extension() . ';base64,' . base64_encode(file_get_contents($request->image));
        $this->post->description = $request->description;
        $this->post->save();

        # 4. Store the categories (We will use the createMany() method)
        # For example: category[1, 3, 5]
        foreach ($request->category as $category_id) {
            $category_post[] = ['category_id' => $category_id];
            # $category_post[1,3,5]
        }
        $this->post->categoryPost()->createMany($category_post);
        # Note that createMany() accepts an array ($category_post) and it is being save to the category_post table

        # Explanation
            # Given:
                //$this->post->id = 1
                //$request->category = [1,3,5]

            # After the foreach loop:
                // $category_post = [
                //  ['category_id => 1],
                //  ['category_id => 3],
                //  ['category_id => 5],
                // ]

            # After the $this->post->categoryPost()
                //    $category_post = [
                //      ['category_id => 1, 'post_id' => 1]
                //      ['category_id => 3, 'post_id' => 1]
                //      ['category_id => 5, 'post_id' => 1]
                //    ]



        # 5. Go back to the homepage
        return redirect()->route('index');
    }

    public function show($id)
    {
        $post = $this->post->findOrFail($id);
        // The same as "SELECT * FROM posts WHERE id = $id"
        return view('users.posts.show')->with('post', $post);
    }

    // edit method is use to display the edit.blade.php
    public function edit($id)
    {
        //1,3,5
        $post = $this->post->findOrFail($id);
        // The same as "SELECT * FROM posts WHERE id = $id"

        // if the user is not the OWNER of the post, then redirect to the homepage
        if (Auth::user()->id != $post->user->id) {
            return redirect()->route('index'); //homepage
        }

        $all_categories = $this->category->all();

        # Get all the category IDs of this specific post. Save this into an array
        $selected_categories = []; //null
        foreach ($post->categoryPost as $category_post) {
            $selected_categories[] = $category_post->category_id;
            //$selected_categories[1,3,5]
        }

        return view('users.posts.edit')
            ->with('post', $post)
            ->with('all_categories', $all_categories)
            ->with('selected_categories', $selected_categories);

    }

    //update method is use to do the actual update of the resource
    public function update(Request $request, $id)
    {
        #  1. validate the data first
        $request->validate([
            'category'     => 'required|array|between:1,3',
            'description'  => 'required|min:1|max:1000',
            'image'        => 'mimes:jpeg,jpg,png,gif|max:1048'
        ]);

        # 2. Update the post details
        $post = $this->post->findOrFail($id);
        $post->description = $request->description;

        // Check for the image, if there is a new image uploaded by the user
        if ($request->image) {
            $post->image = 'data:image/' . $request->image->extension() . ';base64,' . base64_encode(file_get_contents($request->image));
        }

        # 3. Save the post details
        $post->save();


        //Categories
        # 4. Delete all records from the category_post related to this post
        $post->categoryPost()->delete();
        //Use the relationship Post::categoryPost() to select the records related to a post
        //This is equivalent to: DELETE FROM category_post WHERE id = $id

        # 5. Save the new selected categories into the category_post table (PIVOT table)
        foreach ($request->category as $category_id) {
            $category_post[] = ['category_id' => $category_id];
        }
        $post->categoryPost()->createMany($category_post);

        # Return to show post page (just to confirm the update)
        return redirect()->route('post.show', $id);

        # Activity - destroy
        # 1. Create the destroy method
        # 2. Create the route
        # 3. check the file where you should use the route

    }

    public function destroy($id)
    {
        $post = $this->post->findOrFail($id);
        $post->forceDelete(); //this will enable to regular user to force delete / totally delete his/her own post
        return redirect()->back();
    }
}
