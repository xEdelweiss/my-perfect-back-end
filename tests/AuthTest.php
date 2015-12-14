<?php

use App\Models\User;

class AuthTest extends TestCase
{
    public function testRegisterWithValidationErrors()
    {
        $this
            ->logout()
            ->request('POST', 'auth/register', [
                'json' => [],
            ])
            ->assertStatus(400);
    }

    public function testUserInfoByGuest()
    {
        $this
            ->request('GET', 'auth/user')
            ->assertStatus(200)
            ->assertKeysNotExist([
                'result.data.id',
                'result.data.name',
                'result.data.email',
            ])
            ->getId();
    }

    public function testRegister()
    {
        $password = str_random(10);

        /** @var User $user */
        $user = factory(\App\Models\User::class)->make();

        $this
            ->logout()
            ->request('POST', 'auth/register', [
                'json' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $password,
                    'password_confirmation' => $password,
                ],
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.id')
            ->assertDataKeysEqual([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function testLoginWithWrongCredentials()
    {
        /** @var User $user */
        $user = factory(\App\Models\User::class)->create();

        $this
            ->logout()
            ->request('POST', 'auth/login', [
                'json' => [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ],
            ])
            ->assertStatus(400)
            ->assertKeyNotExists('result.data.id');
    }

    public function testLoginLogout()
    {
        $password = str_random(10);

        /** @var User $user */
        $user = factory(\App\Models\User::class)->create([
            'password' => bcrypt($password),
        ]);

        $this
            ->logout()
            ->request('POST', 'auth/login', [
                'json' => [
                    'email' => $user->email,
                    'password' => $password,
                ],
            ])
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $this
            ->request('GET', 'auth/user')
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $this
            ->request('GET', 'auth/logout')
            ->assertStatus(200);

        // user info after logout
        $this
            ->request('GET', 'auth/user')
            ->assertStatus(200)
            ->assertKeysNotExist([
                'result.data.id',
                'result.data.name',
                'result.data.email',
            ])
            ->getId();
    }
}
