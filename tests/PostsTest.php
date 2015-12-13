<?php

class PostsTest extends TestCase
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    /** @var Faker\Generator $faker */
    protected $faker;

    public function setUp()
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => $this->baseUrl . '/api/',
            'http_errors' => false,
            'cookies' => true,
        ]);

        $this->faker = Faker\Factory::create();
    }

    public function testCreateUnauthorized()
    {
        $response = $this->client->request('POST', 'posts', [
            'json' => $this->modelData(),
            'cookies' => null,
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testCreate()
    {
        $this->login();

        $response = $this->client->request('POST', 'posts', [
            'json' => $this->modelData(),
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array
     */
    protected function modelData()
    {
        return [
            'title' => $this->faker->sentence,
            'intro' => $this->faker->paragraph,
            'text' => $this->faker->text,
            'tags' => $this->faker->words(rand(0, 3)),
        ];
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function login()
    {
        return $this->client->get('/dev/fakelogin');
    }
}
