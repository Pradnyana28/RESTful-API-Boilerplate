<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'store']]);
    }

    public function index()
    {
        $user = User::with('permission')->get();
        return $this->showAll($user);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|digits_between:5,21',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->all(), 422);
        }

        // get all request
        $data = $request->all();
        $data['password'] = User::generatePassword($request->password);
        if ($request->has('pp') && !empty($data['pp'])) {
            $image = $data['pp'];
            $name = md5($data['name']) .'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $uploadPP = Image::make($image)->fit(160, 160)->save('images/'. $name);
            $resizedPP = Image::make($image)
                            ->fit(80,80)
                            ->save('images/thumbs/'. $name);
            $data['pp'] = config('app.url') .'/images/'. $name;
        }
        // save to user
        $users = User::create($data);
        // return response
        return $this->showOne($users, 201);
    }

    public function show(User $user)
    {
        return $this->showOne($user);
    }

    public function update(Request $request, User $user)
    {
        $request = collect($request->all())->filter(function ($value) {
            return null !== $value;
        });

        $rules = [
            'email' => [
                'email',
                Rule::unique('users')->ignore($user->id, 'id_user')->where(function ($query) use ($user) {
                    return $query->where('email', '!=', $user->email);
                })
            ],
            'phone' => 'digits_between:1,21',
            'birthdate' => 'date',
            'id_group' => 'in:'. User::REGULAR_USER .','. User::OPERATOR_USER .','. User::MANAGER_USER,
        ];

        $validator = Validator::make($request->toArray(), $rules);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages()->all(), 422);
        }

        if ($request->has('password')) {
            $user->password = User::generatePassword($request->password);
        }

        if ($request->has('id_group')) {
            if (!$user->isOperator()) {
                return $this->errorResponse('Only operator can change this option.', 409);
            }

            $user->id_group = $request->id_group;
        }

        if ($request->has('blocked')) {
            if (auth()->user()->id_group != 1) {
                return $this->errorResponse('Only operator can change this option.');
            }

            $user->blocked = $request['blocked'];
        }

        if ($request->has('name')) {
            $user->name = $request['name'];
        }

        if ($request->has('email')) {
            $user->email = $request['email'];
        }

        if ($request->has('gender')) {
            $user->gender = $request['gender'];
        }

        if ($request->has('birthdate')) {
            $user->birthdate = $request['birthdate'];
        }

        if ($request->has('address')) {
            $user->address = $request['address'];
        }

        if ($request->has('nationality')) {
            $user->nationality = $request['nationality'];
        }

        if ($request->has('city')) {
            $user->city = $request['city'];
        }

        if ($request->has('postcode')) {
            $user->postcode = $request['postcode'];
        }

        if ($request->has('phone')) {
            $user->phone = $request['phone'];
        }

        if ($request->has('pp') && !empty($request->get('pp'))) {
            $image = $request->get('pp');
            $name = md5($user->name) .'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            $uploadPP = Image::make($image)->fit(160, 160)->save('images/'. $name);
            $resizedPP = Image::make('images/'. $name)
                            ->fit(80,80)
                            ->save('images/thumbs/'. $name);
            // remove previous image
            Storage::delete($user->pp);
            // update pp
            $user->pp = config('app.url') .'/images/'. $name;
        }

        $user->save();
        return $this->showOne($user);
    }

    public function showTransaction($idOrEmail)
    {
        $target = is_numeric($idOrEmail) ? 'id_user' : 'email';
        $transaction = User::with('purchase')->where($target, $idOrEmail)->firstOrFail();
        return $this->showOne($transaction);
    }

    public function checkExtension($givenExtension) {
        return in_array(mb_strtolower($givenExtension, 'UTF-8'), [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp'
        ]);
    }
}
