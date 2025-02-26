<?php
namespace Tests\Feature\Api;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
class FallbackRouteTest extends TestCase
{
    /** @test */
    public function missing_api_routes_should_return_a_json_404()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/api/events/1111');
        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson([
            'message' => 'Not Found.'
        ]);
    }
}
