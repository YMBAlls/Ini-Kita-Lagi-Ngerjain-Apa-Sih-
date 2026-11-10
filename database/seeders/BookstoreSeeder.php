<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookstoreSeeder extends Seeder
{
    private const BUYER_COUNT = 20;

    private const PRODUCT_COUNT = 50;

    private const ORDER_COUNT = 40;

    public function run(): void
    {
        DB::transaction(function (): void {
            $faker = Faker::create('id_ID');
            $faker->seed(20261110);
            $now = CarbonImmutable::now();

            $buyerEmails = [];
            $buyers = [];

            for ($i = 1; $i <= self::BUYER_COUNT; $i++) {
                $email = sprintf('pembeli%03d@pembeli.lentera.test', $i);
                $buyerEmails[] = $email;
                $buyers[] = [
                    'nama' => $faker->name(),
                    'email' => $email,
                    'no_telepon' => '08'.$faker->numerify('##########'),
                    'alamat' => $faker->address(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('pembeli')->insertOrIgnore($buyers);

            $buyerIds = DB::table('pembeli')
                ->whereIn('email', $buyerEmails)
                ->orderBy('email')
                ->pluck('id')
                ->all();

            $bookIds = DB::table('buku')
                ->where('isbn', 'like', '978602%')
                ->orderBy('id')
                ->limit(self::PRODUCT_COUNT)
                ->pluck('id')
                ->all();

            if (count($buyerIds) !== self::BUYER_COUNT || count($bookIds) !== self::PRODUCT_COUNT) {
                throw new \RuntimeException('Bookstore demo data requires 20 demo buyers and at least 50 seeded books.');
            }

            $products = [];

            foreach ($bookIds as $index => $bookId) {
                $products[] = [
                    'buku_id' => $bookId,
                    'harga' => 35000 + ($index * 1250),
                    'stok' => 5 + ($index % 36),
                    'aktif' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('produk_toko')->insertOrIgnore($products);

            $productRows = DB::table('produk_toko')
                ->whereIn('buku_id', $bookIds)
                ->orderBy('buku_id')
                ->get(['id', 'buku_id', 'harga']);

            if ($productRows->count() !== self::PRODUCT_COUNT) {
                throw new \RuntimeException('Could not prepare all demo bookstore products.');
            }

            $productsByBookId = $productRows->keyBy('buku_id');
            $orderNumbers = [];
            $orders = [];

            for ($i = 1; $i <= self::ORDER_COUNT; $i++) {
                $number = sprintf('DUMMY-TOKO-2026-%03d', $i);
                $orderNumbers[] = $number;
                $orderedAt = $now->subDays(($i * 7) % 61);

                $orders[] = [
                    'nomor_pesanan' => $number,
                    'pembeli_id' => $buyerIds[($i - 1) % self::BUYER_COUNT],
                    'status' => ['selesai', 'menunggu_pembayaran', 'diproses', 'dikirim'][$i % 4],
                    'alamat_pengiriman' => $faker->address(),
                    'tanggal_pesanan' => $orderedAt,
                    'created_at' => $orderedAt,
                    'updated_at' => $orderedAt,
                ];
            }

            DB::table('pesanan')->insertOrIgnore($orders);

            $orderIds = DB::table('pesanan')
                ->whereIn('nomor_pesanan', $orderNumbers)
                ->orderBy('nomor_pesanan')
                ->pluck('id')
                ->all();

            if (count($orderIds) !== self::ORDER_COUNT) {
                throw new \RuntimeException('Could not prepare all demo bookstore orders.');
            }

            $details = [];

            foreach ($orderIds as $index => $orderId) {
                foreach ([($index * 2) % self::PRODUCT_COUNT, (($index * 2) + 1) % self::PRODUCT_COUNT] as $productIndex) {
                    $product = $productsByBookId[$bookIds[$productIndex]];

                    $details[] = [
                        'pesanan_id' => $orderId,
                        'produk_toko_id' => $product->id,
                        'jumlah' => 1 + (($index + $productIndex) % 3),
                        'harga_satuan' => $product->harga,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            DB::table('detail_pesanan')->insertOrIgnore($details);
        });

        $this->command?->info(sprintf(
            'Bookstore demo data ready: %d buyers, %d products, %d orders, and %d order items.',
            DB::table('pembeli')->where('email', 'like', 'pembeli%@pembeli.lentera.test')->count(),
            DB::table('produk_toko')
                ->join('buku', 'buku.id', '=', 'produk_toko.buku_id')
                ->where('buku.isbn', 'like', '978602%')
                ->count(),
            DB::table('pesanan')->where('nomor_pesanan', 'like', 'DUMMY-TOKO-2026-%')->count(),
            DB::table('detail_pesanan')
                ->join('pesanan', 'pesanan.id', '=', 'detail_pesanan.pesanan_id')
                ->where('pesanan.nomor_pesanan', 'like', 'DUMMY-TOKO-2026-%')
                ->count()
        ));
    }
}
