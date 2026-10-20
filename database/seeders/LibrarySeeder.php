<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibrarySeeder extends Seeder
{
    private const BOOK_COUNT = 20_000;

    private const MEMBER_COUNT = 10_000;

    private const LOAN_COUNT = 100_000;

    private const CHUNK_SIZE = 1_000;

    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $faker->seed(20261018);

        $this->seedCategories();
        $categoryIds = DB::table('kategori')->orderBy('id')->pluck('id')->all();

        $this->seedBooks($faker, $categoryIds);
        $bookIds = DB::table('buku')
            ->where('isbn', 'like', '978602%')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->seedMembers($faker);
        $memberIds = DB::table('anggota')
            ->where('email', 'like', 'anggota%@anggota.lentera.test')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($categoryIds === [] || count($bookIds) < self::BOOK_COUNT || count($memberIds) < self::MEMBER_COUNT) {
            throw new \RuntimeException('Library categories, books, or members could not be prepared for loan seeding.');
        }

        $this->seedLoans($bookIds, $memberIds);

        $this->command?->info(sprintf(
            'Library data seeded: %d categories, %d books, %d members, and at least %d loans (%d+ rows total).',
            count($categoryIds),
            self::BOOK_COUNT,
            self::MEMBER_COUNT,
            self::LOAN_COUNT,
            count($categoryIds) + self::BOOK_COUNT + self::MEMBER_COUNT + self::LOAN_COUNT
        ));
    }

    private function seedCategories(): void
    {
        $categories = [
            'Fiksi',
            'Nonfiksi',
            'Pendidikan',
            'Sains',
            'Teknologi',
            'Sejarah',
            'Biografi',
            'Anak',
            'Sastra',
            'Agama',
            'Bisnis',
            'Seni',
        ];

        DB::table('kategori')->insertOrIgnore(array_map(
            fn (string $name) => ['nama' => $name, 'deskripsi' => "Buku kategori {$name}."],
            $categories
        ));
    }

    private function seedBooks(Generator $faker, array $categoryIds): void
    {
        $prefix = '978602';
        $rows = [];

        for ($i = 1; $i <= self::BOOK_COUNT; $i++) {
            $isbn = $prefix.str_pad((string) $i, 10, '0', STR_PAD_LEFT);

            $rows[] = [
                'kategori_id' => $categoryIds[($i - 1) % count($categoryIds)],
                'judul' => $faker->sentence(4),
                'penulis' => $faker->name(),
                'penerbit' => $faker->company(),
                'tahun_terbit' => $faker->numberBetween(1950, (int) date('Y')),
                'isbn' => $isbn,
                'stok' => $faker->numberBetween(0, 50),
            ];

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('buku')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('buku')->insertOrIgnore($rows);
        }
    }

    private function seedMembers(Generator $faker): void
    {
        $rows = [];
        $today = CarbonImmutable::today();

        for ($i = 0; $i < self::MEMBER_COUNT; $i++) {
            $email = sprintf('anggota%05d@anggota.lentera.test', $i);

            $rows[] = [
                'nama' => $faker->name(),
                'email' => $email,
                'no_telepon' => '08'.$faker->numerify('##########'),
                'alamat' => $faker->address(),
                'tanggal_daftar' => $today->subDays($faker->numberBetween(0, 3_650))->toDateString(),
            ];

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('anggota')->insertOrIgnore($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('anggota')->insertOrIgnore($rows);
        }
    }

    private function seedLoans(array $bookIds, array $memberIds): void
    {
        $remaining = max(0, self::LOAN_COUNT - DB::table('peminjaman')->count());
        $rows = [];
        $today = CarbonImmutable::today();

        for ($i = 0; $i < $remaining; $i++) {
            $daysAgo = random_int(1, 730);
            $loanDate = $today->subDays($daysAgo);
            $status = $daysAgo > 14 ? 'terlambat' : 'dipinjam';
            $returnedDate = null;

            if (random_int(1, 100) <= 70) {
                $status = 'dikembalikan';
                $returnedDate = $loanDate
                    ->addDays(random_int(1, min(14, $daysAgo)))
                    ->toDateString();
            }

            $rows[] = [
                'anggota_id' => $memberIds[array_rand($memberIds)],
                'buku_id' => $bookIds[array_rand($bookIds)],
                'tanggal_pinjam' => $loanDate->toDateString(),
                'tanggal_jatuh_tempo' => $loanDate->addDays(14)->toDateString(),
                'tanggal_kembali' => $returnedDate,
                'status' => $status,
            ];

            if (count($rows) >= self::CHUNK_SIZE) {
                DB::table('peminjaman')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('peminjaman')->insert($rows);
        }
    }
}
