<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\BatchTryouts;
use App\Models\KompetensiTryouts;
use App\Models\BidangTryouts;
use App\Models\SoalTryout;

class DatabaseSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker::create('id_ID'); // Inisialisasi faker
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin user
        User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'), // Hash password
        ]);

        // Buat batch tryout
        $batch = BatchTryouts::firstOrCreate([
            'nama' => 'Batch 1',
            'start_date' => now(),
            'end_date' => now()->addDays(34),
        ]);

        // Buat tryout
        $tryout = \App\Models\Tryouts::firstOrCreate([
            'nama' => 'TRY OUT FDI 1 BATCH 1 2024',
            'batch_id' => $batch->id,
            'tanggal' => now()->subDays(3),
            'waktu' => 120,
        ]);

        // Buat kompetensi dan bidang
        $kompetensi = KompetensiTryouts::firstOrCreate([
            'nama' => '1',
        ]);

        $bidang = BidangTryouts::firstOrCreate([
            'nama' => 'INTERNA',
        ]);

        for ($i = 1; $i <= 150; $i++) {
            SoalTryout::firstOrCreate([
                'nomor' => $i,
                'tryout_id' => $tryout->id,
                'soal' => $this->faker->paragraphs(2, true),
                'pilihan_a' => $this->faker->word(),
                'pilihan_b' => $this->faker->word(),
                'pilihan_c' => $this->faker->word(),
                'pilihan_d' => $this->faker->word(),
                'pilihan_e' => $this->faker->word(),
                'jawaban' => 'A', // Jawaban tetap
                'bidang_id' => $bidang->id,
                'kompetensi_id' => $kompetensi->id,
            ]);
        }
    }
}
