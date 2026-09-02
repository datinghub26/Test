<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostbackTest extends TestCase
{
    public function test_approve_postback(): void
    {
        $response = $this->post(route('postback.adgem'), [
            'user_id' => 1,
            'campaign_id' => rand(1213414, 9999999),
            'campaign_name' => 'Test Campaign',
            'amount' => 500,
            'payout' => 1.00,
            'ip' => fake()->ipv4,
        ]);

        $response->assertStatus(200);
        $response->assertSee('1');
    }

    public function test_chargeback_postback(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


}
