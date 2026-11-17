<?php

namespace Tests\Feature;

use App\Models\Stand;
use App\Models\User;
use App\Notifications\StandApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CdJajanTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_page_loads_with_active_stands(): void
    {
        $seller = User::create([
            'name' => 'Pak Budi',
            'email' => 'budi@cd.test',
            'password' => 'password',
            'role' => 'seller',
            'status' => 'approved',
        ]);

        $stand = $seller->stand()->create([
            'name' => 'Bakso Pak Budi',
            'category' => 'Makanan Berat',
            'location' => 'Blok A-2',
            'whatsapp' => '6281234567890',
            'is_active' => true,
        ]);

        $stand->products()->create([
            'name' => 'Bakso Urat',
            'price' => 15000,
            'is_available' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bakso Pak Budi');
        $response->assertSee('Bakso Urat');
    }

    public function test_seller_registration_creates_pending_stand_and_admin_can_approve(): void
    {
        Notification::fake();

        $admin = User::create([
            'name' => 'Admin CD',
            'email' => 'admin@cd.test',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $registerResponse = $this->post('/daftar', [
            'name' => 'Siti',
            'stand_name' => 'Es Buah Siti',
            'category' => 'Minuman',
            'location' => 'Blok B-1',
            'whatsapp' => '081234567899',
            'description' => 'Es buah segar',
            'email' => 'siti@cd.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse->assertRedirect(route('register.done'));

        $seller = User::where('email', 'siti@cd.test')->firstOrFail();
        $this->assertEquals('pending', $seller->status);
        $this->assertFalse($seller->stand->is_active);

        // Belum bisa login saat pending
        $this->post('/masuk', [
            'email' => 'siti@cd.test',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        // Admin menerima pengajuan
        $this->actingAs($admin)
            ->post(route('admin.approve', $seller))
            ->assertRedirect();

        $seller->refresh();
        $this->assertEquals('approved', $seller->status);
        $this->assertTrue($seller->stand->is_active);
        Notification::assertSentTo($seller, StandApproved::class);
    }

    public function test_seller_can_manage_own_products_only(): void
    {
        $sellerA = User::create([
            'name' => 'Seller A',
            'email' => 'a@cd.test',
            'password' => 'password',
            'role' => 'seller',
            'status' => 'approved',
        ]);
        $standA = $sellerA->stand()->create([
            'name' => 'Stand A',
            'category' => 'Camilan',
            'location' => 'Blok A',
            'whatsapp' => '628111111111',
            'is_active' => true,
        ]);

        $sellerB = User::create([
            'name' => 'Seller B',
            'email' => 'b@cd.test',
            'password' => 'password',
            'role' => 'seller',
            'status' => 'approved',
        ]);
        $standB = $sellerB->stand()->create([
            'name' => 'Stand B',
            'category' => 'Minuman',
            'location' => 'Blok B',
            'whatsapp' => '628222222222',
            'is_active' => true,
        ]);
        $productB = $standB->products()->create([
            'name' => 'Jus Alpukat',
            'price' => 12000,
            'is_available' => true,
        ]);

        // Seller A menambah produk sendiri
        $this->actingAs($sellerA)
            ->post(route('seller.products.store'), [
                'name' => 'Tahu Crispy',
                'price' => 10000,
                'description' => 'Renyaah',
                'is_available' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', [
            'stand_id' => $standA->id,
            'name' => 'Tahu Crispy',
            'price' => 10000,
        ]);

        // Seller A tidak bisa mengedit produk milik Seller B
        $this->actingAs($sellerA)
            ->put(route('seller.products.update', $productB->id), [
                'name' => 'Hack',
                'price' => 1000,
                'is_available' => '1',
            ])
            ->assertNotFound();
    }
}
