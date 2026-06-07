<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\MulazamahYearly;
use App\Models\MulazamahMonthly;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Check if DB is already seeded
        if (\App\Models\User::count() > 0) {
            $this->command->info('Database is already seeded. Skipping.');
            return;
        }

        // 1. Create Admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('UntukIndonesiaRaya123'),
            'role' => 'admin',
        ]);

        // 2. Parse CSV
        $filePath = database_path('data.csv');
        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        fgetcsv($file);
        fgetcsv($file);

        $hijriMonths = [
            'Muharrom', 'Shofar', 'Robi\'ul Awwal', 'Robi\'ul Akhir',
            'Jumadil Ula', 'Jumadil Akhir', 'Rojab', 'Sya\'ban',
            'Romadhon', 'Syawwal', 'Dzulqo\'dah', 'Dzulhijjah'
        ];

        $usersToInsert = [];
        $studentsToInsert = [];
        $yearliesToInsert = [];
        $monthliesToInsert = [];

        // Users auto-increment starts at 2 (since Admin is ID 1)
        $nextUserId = 2;
        // Students auto-increment starts at 1
        $nextStudentId = 1;

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 22 || empty($row[0])) continue;

            $name = trim($row[0]);
            $username = trim($row[1]);
            $password = trim($row[2]);
            $gender = trim($row[3]) === 'P' ? 'P' : 'L';

            $totalTerbayarAll = (int) preg_replace('/[^0-9]/', '', $row[19]);
            $totalKekuranganAll = (int) preg_replace('/[^0-9]/', '', $row[20]);
            $totalSeharusnyaAll = (int) preg_replace('/[^0-9]/', '', $row[21]);

            $currentUserId = $nextUserId++;
            $currentStudentId = $nextStudentId++;

            $usersToInsert[] = [
                'id' => $currentUserId,
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'murid',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $studentsToInsert[] = [
                'id' => $currentStudentId,
                'user_id' => $currentUserId,
                'gender' => $gender,
                'total_paid' => $totalTerbayarAll,
                'total_debt' => $totalKekuranganAll,
                'total_expected' => $totalSeharusnyaAll,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $years = [
                1446 => ['paid' => 4, 'debt' => 5, 'expected' => 6],
                1447 => ['paid' => 7, 'debt' => 8, 'expected' => 9],
                1448 => ['paid' => 10, 'debt' => 11, 'expected' => 12],
                1449 => ['paid' => 13, 'debt' => 14, 'expected' => 15],
                1450 => ['paid' => 16, 'debt' => 17, 'expected' => 18],
            ];

            foreach ($years as $year => $cols) {
                $paid = (int) preg_replace('/[^0-9]/', '', $row[$cols['paid']]);
                $debt = (int) preg_replace('/[^0-9]/', '', $row[$cols['debt']]);
                $expected = (int) preg_replace('/[^0-9]/', '', $row[$cols['expected']]);

                $yearliesToInsert[] = [
                    'student_id' => $currentStudentId,
                    'hijri_year' => $year,
                    'total_paid' => $paid,
                    'total_debt' => $debt,
                    'total_expected' => $expected,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $monthsPaid = floor($paid / 125000);

                for ($i = 0; $i < 12; $i++) {
                    $isPaid = $monthsPaid > 0 ? 'true' : 'false';
                    $monthliesToInsert[] = [
                        'student_id' => $currentStudentId,
                        'hijri_year' => $year,
                        'hijri_month' => $hijriMonths[$i],
                        'is_paid' => $isPaid,
                        'amount' => 125000,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $monthsPaid--;
                }
            }
        }

        fclose($file);

        // BULK INSERT EVERYTHING to avoid 30s timeout on slow remote connections!
        foreach (array_chunk($usersToInsert, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('users')->insert($chunk);
        }
        foreach (array_chunk($studentsToInsert, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('students')->insert($chunk);
        }
        foreach (array_chunk($yearliesToInsert, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('mulazamah_yearlies')->insert($chunk);
        }
        foreach (array_chunk($monthliesToInsert, 500) as $chunk) {
            \Illuminate\Support\Facades\DB::table('mulazamah_monthlies')->insert($chunk);
        }
        
        // Reset sequence for PostgreSQL since we inserted IDs manually
        \Illuminate\Support\Facades\DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users))");
        \Illuminate\Support\Facades\DB::statement("SELECT setval('students_id_seq', (SELECT MAX(id) FROM students))");
    }
}
