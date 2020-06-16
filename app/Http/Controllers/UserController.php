<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\UserUpdateRequest;
use App\Service\UserService;
use Illuminate\Support\Facades\Auth;

/**
 * ユーザーに関する処理を行うコントローラクラス
 */
class UserController extends Controller
{
    /**
     * ユーザーに関するサービスクラスのインスタンス
     *
     * @var \App\Service\UserService
     */
    private $userService;

    /**
     * コンストラクタ
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $posts = $this->userService->getPosts($user->id);

        return view('users.show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // ユーザー確認
        if ($user->id !== Auth::id()) {
            return redirect()->route('users.show', Auth::user())->withErrors('適切なユーザーでログインしてください')->withInput();
        }

        $this->userService->setUserForEdit($user);
        return view('users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->userService->updateUser($request, $user);
        return redirect('/users/' . $user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
    }
}
