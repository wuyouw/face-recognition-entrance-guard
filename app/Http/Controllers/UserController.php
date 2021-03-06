<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserInfo;
use App\Http\Resources\UserOwnerResource;
use App\Http\Resources\UserPropertyResource;
use App\Http\Resources\UserVisiterResource;
use App\Visit;
use App\Role;
use App\UserAddress;

class UserController extends Controller
{
    /**
     * 根据token获取用户信息
     *
     * @return \Illuminate\Http\Response
     */
    public function user_info()
    {
        return new UserInfo(Auth::user());
    }
    /**
     * 管理员获取所有业主信息
     *
     * @return \Illuminate\Http\Response
     */
    public function user_owner()
    {
        // dd(Auth::user()->roles);
        $flag = false;
        foreach(Auth::user()->roles as $role) {
            if($role->name == 'admin' || $role->name == 'property'){
                $flag = true;
                break;
            }
        }
        if($flag){
            $userCollection = collect([]);
            foreach(User::all() as $user) {
                foreach($user->addresses as $address) {
                    if($address->pivot->role_id == 5){
                        $user->address_1 = $address;
                        $user->visiter_num = count($address->users)-1;
                        $userCollection->push($user);
                    }
                }
            }
            // dd($userCollection);
            return  UserOwnerResource::collection($userCollection);
        }
        // return new UserInfo(Auth::user());
        return "false";
    }

    public function user_property()
    {
        // return "ok";
        // dd(Auth::user()->roles);
        $flag = false;
        foreach(Auth::user()->roles as $role) {
            if($role->name == 'admin' || $role->name == 'property'){
                $flag = true;
                break;
            }
        }
        if($flag){
            $userCollection = collect([]);
            foreach(User::all() as $user) {
                $flag2 = false;
                foreach($user->roles as $role) {

                    if($role->name == 'admin' || $role->name == 'property' || $role->name == 'security' ){
                        $flag2 = true;
                    }
                }
                if($flag2) {
                    $user->role = $user->roles->first()->alias;
                    $userCollection->push($user);
                }
                // if($user->roles->has(1) || )
                // foreach($user->addresses as $address) {
                //     if($address->pivot->role_id == 5){
                //         $user->address_1 = $address;
                //         $user->visiter_num = count($address->users)-1;
                //         $userCollection->push($user);
                //     }
                // }
            }
            // dd($userCollection);
            return  UserPropertyResource::collection($userCollection);
        }
        // return new UserInfo(Auth::user());
        return "false";
    }

    public function user_visiter()
    {
        // return "ok";
        // dd(Auth::user()->roles);
        $flag = false;
        foreach(Auth::user()->roles as $role) {
            if($role->name == 'admin' || $role->name == 'property'){
                $flag = true;
                break;
            }
        }
        if($flag){
            $userCollection = collect([]);
            foreach(UserAddress::all() as $userAddress) {
                $user;
                $flag2 = false;
                $role = Role::find($userAddress->role_id);
                    if($role->name == 'family' || $role->name == 'relative' || $role->name == 'nanny' || $role->name == 'temporary' ){
                        $flag2 = true;
                    }
                if($flag2){
                    $userCollection->push($userAddress);
                }
            }
            // dd($userCollection);
            return  UserVisiterResource::collection($userCollection);
        }
        // return new UserInfo(Auth::user());
        return "false";
    }
    

    /**
     * 列出所有用户
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UserCollection(User::all());
    }

    /**
     * 新建一个用户
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user = User::create($request->all());
        // dd($User->id);
        return new UserResource($user);

    }

    /**
     * 获取某个用户的信息
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * 更新某个资源
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return new UserResource($user);
    }

    /**
     * 删除某个用户
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $result = $user->delete();
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * 列出所有用户
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        return $user->name;
    }
}
