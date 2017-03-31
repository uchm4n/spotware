<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\Facades\JWTAuth;


class BXBooks extends TestCase
{

    private $credentials = [ 'email' => 'demo@example.com', 'password' => 12345 ];
    private $token;

    private $jsonStructureAll = [
        'data' => [
            '*' =>
                [
                    "ISBN","Title","Author","Year","Publisher","ImgS","ImgM","ImgL",
                    "users" =>
                        [
                            "data" =>
                                [
                                    '*' => ['User-ID','Location','Age','Rating']
                                ]
                        ]
                ]
        ],
        "meta" =>
            [
                "pagination" =>
                    [
                        "total","count","per_page","current_page","total_pages",
                        "links" => ["next"]
                    ]
            ]
    ];


    /**
     * Return request headers needed to interact with the API.
     *
     * @param null $credentials
     * @return array of headers.
     */
    protected function headers($credentials = null)
    {
        $headers = ['Accept' => 'application/json'];

        if (is_null($credentials)) {
            $credentials = $this->credentials;
        }
        $token = JWTAuth::attempt($credentials);
        $headers['Authorization'] = 'Bearer ' . $token;

        return ($headers);
    }




    /**
     * @test
     * Test All returned books and json structure
     */
    public function return_all_books()
    {

        $this->get('/api/bx_books/')
            ->assertStatus(200)
            ->assertJsonStructure($this->jsonStructureAll);

        $this->get('/api/bx_books?page=2')
            ->assertStatus(200)
            ->assertJsonStructure($this->jsonStructureAll);

    }

    /**
     * @test
     * Test individual book and it's json structure
     */
    public function return_book_by_isbn()
    {
        $this->get('/api/bx_books/1857022424')
            ->assertStatus(200)
            ->assertJsonStructure($this->jsonStructureAll['data']);
    }


    /**
     * @test
     * Test login
     */
    public function login()
    {
        $res = $this->post('/api/auth/',$this->credentials)
            ->assertStatus(200)
            ->assertJsonStructure(['token']);

        return $this->token = $res->getContent();
    }



    /**
     * @test
     * Test book update with JWT authorization headers
     */
    public function book_update()
    {
        $headers = $this->headers();

        $this->put('/api/bx_books/update/1857022424',[
            'Title' => 'Test Case Run at: ' . date('Y/m/d H:i:s'),
            'Author' => 'Test Case',
            'Year' => date('Y'),
            'Publisher' => 'Spotware',
            'ImgS' => 'https://placehold.it/200x400',
            'ImgM' => 'https://placehold.it/200x400',
            'ImgL' => 'https://placehold.it/200x400',
        ],$headers);
    }

}
