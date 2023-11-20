<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class AddDefaultValuesSeeder extends Seeder
{

    private $category;
    private $user;

    /**
     * Run the database seeds.
     */

    public function __construct(Category $category, User $user){
        $this->category = $category;
        $this->user = $user;
    }

    public function run(): void
    {
        $categories = [
            'name'         => 'Javascript Programming',
            'created_at'   => NOW(),
            'updated_at'   => NOW()
        ];
        $this->category->insert($categories);


        $this->user->name        = 'Administrator1';
        $this->user->email       = 'admin1@gmail.com';
        $this->user->password    = Hash::make('admin123456');
        $this->user->role_id     = User::ADMIN_ROLE_ID; //1
        $this->user->save();
    }
}
