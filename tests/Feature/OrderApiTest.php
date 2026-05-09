<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_order(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer test-token')
            ->postJson('/api/orders', [
                'external_id' => 'tiendanube-1001',
                'customer_name' => 'Ada Lovelace',
                'customer_email' => 'ada@example.com',
                'shipping_address' => 'Calle Falsa 123, Buenos Aires',
                'status' => 'pending',
                'currency' => 'ARS',
                'total_amount' => 14999.90,
                'items' => [
                    [
                        'sku' => 'SKU-01',
                        'name' => 'Caja Urbana',
                        'quantity' => 2,
                        'price' => 7499.95,
                    ],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.customer_email', 'ada@example.com')
            ->assertJsonPath('data.items.0.sku', 'SKU-01');

        $this->assertDatabaseHas('orders', [
            'external_id' => 'tiendanube-1001',
            'customer_name' => 'Ada Lovelace',
        ]);
    }

    public function test_it_requires_a_valid_token(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertUnauthorized();
    }
}
