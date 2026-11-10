<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BukuSeeder extends Seeder
{
    private const TARGET_TOTAL = 150_000;

    private const CHUNK_SIZE = 1_000;

    public function run(): void
    {
        $currentTotal = DB::table('buku')->count();
        $remaining = self::TARGET_TOTAL - $currentTotal;

        if ($remaining <= 0) {
            $this->command?->info("Tabel buku sudah berisi {$currentTotal} data; target tidak memerlukan tambahan.");

            return;
        }

        if (! DB::table('kategori')->exists()) {
            throw new RuntimeException('Tabel kategori kosong. Isi kategori sebelum membuat data buku.');
        }

        $maxIsbnSequence = (int) DB::table('buku')
            ->where('isbn', 'like', '978602%')
            ->selectRaw('MAX(CAST(SUBSTRING(isbn, 7) AS UNSIGNED)) AS max_sequence')
            ->value('max_sequence');

        $nextIsbnSequence = $maxIsbnSequence + 1;
        $this->command?->info("Menambahkan {$remaining} data dummy buku (target total: ".self::TARGET_TOTAL.').');

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);
            $books = Buku::factory()->count($batchSize)->make();
            $rows = [];

            foreach ($books as $book) {
                if ($nextIsbnSequence > 9_999_999_999) {
                    throw new RuntimeException('ISBN demo prefix 978602 has reached its supported sequence limit.');
                }

                $book->setAttribute(
                    'isbn',
                    '978602'.str_pad((string) $nextIsbnSequence++, 10, '0', STR_PAD_LEFT)
                );

                $rows[] = $book->getAttributes();
            }

            DB::table('buku')->insert($rows);
            $remaining -= $batchSize;
        }

        $total = DB::table('buku')->count();
        $this->command?->info("Selesai! Tabel buku sekarang memiliki {$total} data.");
    }
}
