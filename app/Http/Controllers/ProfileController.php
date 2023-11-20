<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function show($id){
        $user = $this->user->findOrFail($id);
        return view('users.profile.show')
            ->with('user', $user);
    }

    public function edit(){
        $user = $this->user->findOrFail(Auth::user()->id);
        #$user[id, name,email,introduction,created_at, updated_at]
        return view('users.profile.edit')
            ->with('user', $user);
    }

    public function update(Request $request){
        # 1. Validate the data first
        $request->validate([
            'name'         => 'required|min:1|max:50',
            'email'        => 'required|email|max:50|unique:users,email,' . Auth::user()->id,
            'avatar'       => 'mimes:jpeg,jpg,gif,png|max:1048',
            'introduction' => 'max:100'
        ]);

        # 2. Save new user details into the table
        $user               = $this->user->findOrFail(Auth::user()->id);
        $user->name         = $request->name;
        $user->email        = $request->email;
        $user->introduction = $request->introduction;

        # 3. Check if there new image/avatar uploaded
        if ($request->avatar) {
            //convert to base64 encoding and
            //save to the table the new image/avatar if available
            $user->avatar = 'data:avatar/' . $request->avatar->extension() . ';base64,' .base64_encode(file_get_contents($request->avatar));
        }

        # 4. Execute the save() method
        $user->save();

        # 5. redirect into profile page if the update is successful
        return redirect()->route('profile.show', Auth::user()->id);
    }
}
