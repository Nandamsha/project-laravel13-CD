<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Data contoh. Semua akun memakai kata sandi: password
     * Ganti kata sandi admin sebelum dipakai sungguhan.
     */
    public function run(): void
    {
        User::create(['name' => 'Admin CD', 'email' => 'admin@cd.test', 'password' => 'password', 'role' => 'admin', 'status' => 'approved']);

        $data = [
            ['sate@cd.test', 'Pak Dedi', 'Sate Pak Dedi', 'Makanan Berat', 'Blok A-1, dekat gerbang utama', '6281200000001', 'Sate ayam dan kambing dibakar arang, bumbu kacang kental.', [
                ['Sate Ayam 10 tusuk', 20000, 'Dengan lontong dan bumbu kacang.', true],
                ['Sate Kambing 5 tusuk', 25000, 'Daging empuk, tanpa bau prengus.', true],
                ['Lontong Tambahan', 4000, 'Satu porsi lontong.', false]]],
            ['kopi@cd.test', 'Mbak Sari', 'Kopi Senja', 'Minuman', 'Blok B-3, sebelah panggung', '6281200000002', 'Kopi susu, es teh, dan minuman segar untuk nongkrong malam.', [
                ['Kopi Susu Gula Aren', 15000, 'Dingin, manis sedang.', true],
                ['Es Teh Lemon', 8000, 'Teh seduh dengan perasan lemon.', true]]],
            ['cilok@cd.test', 'Mang Ujang', 'Cilok Mang Ujang', 'Camilan', 'Blok A-4, depan parkiran motor', '6281200000003', 'Cilok, batagor, dan siomay dengan saus pedas buatan sendiri.', [
                ['Cilok Isi 10', 10000, 'Pilih saus pedas atau kacang.', true],
                ['Batagor Kuah', 13000, 'Batagor ikan dengan kuah asam pedas.', true]]],
            ['waffle@cd.test', 'Nona', 'Waffle Nona', 'Dessert', 'Blok C-2, area lesehan', '6281200000004', 'Waffle hangat dengan topping cokelat, keju, dan buah.', [
                ['Waffle Cokelat Keju', 17000, 'Waffle tebal, meses dan keju parut.', true],
                ['Waffle Es Krim', 22000, 'Satu scoop es krim vanila.', true]]],
            ['nasgor@cd.test', 'Bang Jali', 'Nasi Goreng Bang Jali', 'Makanan Berat', 'Blok C-5, pojok barat', '6281200000005', 'Nasi goreng, mie goreng, dan kwetiau yang dimasak saat dipesan.', [
                ['Nasi Goreng Spesial', 18000, 'Telur, ayam suwir, kerupuk.', true],
                ['Mie Goreng Jawa', 16000, 'Bumbu manis gurih, pedas bisa diatur.', true]]],
            ['lila@cd.test', 'Lila', 'Aksesoris Lila', 'Lainnya', 'Blok D-1, dekat toilet', '6281200000006', 'Gantungan kunci, jepit rambut, dan pernak-pernik lucu.', [
                ['Gantungan Kunci Rajut', 12000, 'Aneka karakter, buatan tangan.', true]]],
        ];

        foreach ($data as [$email, $owner, $name, $cat, $loc, $wa, $desc, $products]) {
            $user = User::create(['name' => $owner, 'email' => $email, 'password' => 'password', 'role' => 'seller', 'status' => 'approved']);
            $stand = $user->stand()->create(['name' => $name, 'category' => $cat, 'location' => $loc, 'whatsapp' => $wa, 'description' => $desc, 'is_active' => true]);
            foreach ($products as [$pn, $price, $pd, $ok]) {
                $stand->products()->create(['name' => $pn, 'price' => $price, 'description' => $pd, 'is_available' => $ok]);
            }
        }

        // Contoh pengajuan yang masih di daftar tunggu
        $pending = User::create(['name' => 'Rina', 'email' => 'pending@cd.test', 'password' => 'password', 'role' => 'seller', 'status' => 'pending']);
        $pending->stand()->create(['name' => 'Mie Ayam Bu Rina', 'category' => 'Makanan Berat', 'location' => 'Blok B-6, dekat musala', 'whatsapp' => '6281200000007', 'description' => 'Mie ayam dan pangsit goreng.', 'is_active' => false]);
    }
}
