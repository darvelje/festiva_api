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
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{

    public function getRentalhoUrl()
    {
        return env('SERVER_API');
    }

    protected function getUserByToken($token)
    {
        $response = Http::withToken($token)->acceptJson()->get(env('SERVER_API')."/api/oauth/token-info");

        return json_decode($response->getBody()->getContents(), true);
    }

    //section Get_Token_User
    public function getTokenUser(Request $request){

        $userDriver = $this->getUserByToken($request->token);

        if ($user = User::where('email', $userDriver->email)->first()) {
            return $this->authAndRedirect($user); // Login y redirección
        } else {
            if (!$userDriver->email) { //Si no hay email no nos sirve
                return back()->withErrors(['errors' => 'Tu cuenta no tiene ningún correo asociado']);
            }
            else{
//                $user = User::create([
//                    // 'token' => $user->token;
//                    'name' => $userDriver->getName(),
//                    'nombre' => $userDriver->getName(),
//                    'slug' => Str::random(10),
//                    'tipo' => 'Usuario',
//                    'sponsor' => $afiliado,
//                    'email' => $social_user->getEmail(),
//                    'password' => Hash::make($social_user->id),
//                    'avatar' => $social_user->avatar,
//                    'pin' => $pin,
//                    'provider_id' => $social_user->id,
//                    'provider' => $driver,
//                    'email_verified_at' => now(),
//                ]);
            }
        }

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'TOKEN',
                'token' => $request->token,
                'info' => $this->getUserByToken($request->token)
            ]
        );

    }

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
