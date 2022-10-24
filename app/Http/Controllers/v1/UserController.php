<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    //section Get_Users
    public function getUsers(){

        $users = User::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Users',
                'users' => $users
            ]
        );
    }

    //section Get_User
    public function getUserById(Request $request){

        $user = User::whereId($request->userId)->first();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'User',
                'user' => $user
            ]
        );
    }

    //section New_User
    public function newUser(NewUserRequest $request){

        try{
            DB::beginTransaction();
            $user = new User();

            $user->name = $request->userName;
            $user->last_name = $request->userLastName;
            $user->phone = $request->userPhone;
            $user->email = $request->userEmail;
            $user->password = Hash::make($request->userPassword);
            if ($request->hasFile('avatar')) {
                $user->avatar = self::uploadImage($request->userAvatar, $request->userName);
            }

            $user->save();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User created successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Update_User
    public function updateUser(UpdateUserRequest $request){
        try{
            DB::beginTransaction();

            $user = User::whereId($request->userId)->first();

            $user->name = $request->userName;
            $user->last_name = $request->userLastName;
            $user->phone = $request->userPhone;
            $user->email = $request->userEmail;

            if($request->userPassword){
                $user->password = Hash::make($request->userPassword);
            }
            if ($request->hasFile('avatar')) {
                $user->avatar = self::uploadImage($request->userAvatar, $request->userName);
            }

            $user->update();

            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User updated successfully'
                ]
            );
        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    // section Delete_User
    public function deleteUser(Request $request){
        try {
            DB::beginTransaction();
            User::whereId($request->userId)->delete();
            DB::commit();

            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'User deleted successfully'
                ]
            );

        }
        catch(\Throwable $th){
            return response()->json(
                ['code' => 'error', 'message' => $th->getMessage()]
            );
        }
    }

    //section Upload_image
    public static function uploadImage($path, $name){
        $image = $path;

        $avatarName =  $name . substr(uniqid(rand(), true), 7, 7) . '.webp';

        $img = Image::make($image->getRealPath())->encode('webp', 50)->orientate();

        $img->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->stream();

        Storage::disk('public')->put('/userImages' . '/' . $avatarName, $img, 'public');

        $path = '/userImages/' . $avatarName;

        return $path;
    }
}
