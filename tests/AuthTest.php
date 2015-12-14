<?php

class AuthTest extends TestCase
{
    public function testFlow()
    {
        $modelData = $this->modelData();

        // register with validation errors
        $this
            ->logout()
            ->request('POST', 'auth/register', [
                'json' => [],
            ])
            ->assertStatus(400);

        // user info without authorization
        $this
            ->request('GET', 'auth/user')
            ->assertStatus(200)
            ->assertKeysNotExist([
                'result.data.id',
                'result.data.name',
                'result.data.email',
            ])
            ->getId();

        // register
        $this
            ->logout()
            ->request('POST', 'auth/register', [
                'json' => $modelData,
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.id')
            ->assertDataKeysEqual([
                'name' => $modelData['name'],
                'email' => $modelData['email'],
            ]);

        // login with wrong credentials
        $this
            ->logout()
            ->request('POST', 'auth/login', [
                'json' => [
                    'email' => $modelData['email'],
                    'password' => 'wrong-password',
                ],
            ])
            ->assertStatus(400)
            ->assertKeyNotExists('result.data.id');

        // login
        $userId = $this
            ->logout()
            ->request('POST', 'auth/login', [
                'json' => [
                    'email' => $modelData['email'],
                    'password' => $modelData['password'],
                ],
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.id')
            ->getId();

        // user info
        $this
            ->request('GET', 'auth/user')
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'id' => $userId,
                'name' => $modelData['name'],
                'email' => $modelData['email'],
            ]);

        // logout
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

    /**
     * @return array
     */
    protected function modelData()
    {
        $password = str_random(10);

        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];
    }
}
