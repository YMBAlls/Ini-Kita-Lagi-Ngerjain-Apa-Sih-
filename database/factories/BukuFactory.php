<?php

namespace Database\Factories;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    protected $model = Buku::class;

    /**
     * @var list<int>|null
     */
    private static ?array $categoryIds = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$categoryIds ??= DB::table('kategori')->pluck('id')->all();

        if (self::$categoryIds === []) {
            throw new RuntimeException('Seed categories before generating books.');
        }

        return [
            'kategori_id' => fake()->randomElement(self::$categoryIds),
            'judul' => fake()->sentence(4),
            'penulis' => fake()->name(),
            'penerbit' => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(1950, (int) date('Y')),
            'isbn' => fake()->unique()->numerify('978602##########'),
            'stok' => fake()->numberBetween(0, 50),
        ];
    }
}
