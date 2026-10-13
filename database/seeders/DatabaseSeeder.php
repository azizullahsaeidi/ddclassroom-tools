<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(UserTypeSeeder::class);
        if (User::whereEmail('admin@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Azizullah Saeidi',
                'email' => 'admin@email.com',
            ]);
        }
        if (User::whereEmail('teacher@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'Teacher',
                'email' => 'teacher@email.com',
            ]);
        }
        if (User::whereEmail('zalal@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'ساغر زلال',
                'email' => 'zalal@email.com',
                'signature' => 'saghar-zalal.png',
                'user_type_id' => UserTypeEnum::Teacher,
            ]);
        }
        if (User::whereEmail('hashimi@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'اسدالله هاشمی',
                'signature' => 'hashimi.png',
                'email' => 'hashimi@email.com',
                'user_type_id' => UserTypeEnum::Teacher,
            ]);
        }
        if (User::whereEmail('bahaduri@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'بهادری عبدالستار',
                'signature' => 'boss.png',
                'email' => 'bahaduri@email.com',
                'user_type_id' => UserTypeEnum::Teacher,
            ]);
        }
        if (User::whereEmail('rahimi@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'محمکم‌تاش رحیمی',
                'signature' => 'rahimi.png',
                'email' => 'rahimi@email.com',
                'user_type_id' => UserTypeEnum::Teacher,
            ]);
        }
        if (User::whereEmail('hussain@email.com')->doesntExist()) {
            User::factory()->create([
                'name' => 'حسین مرادی پور',
                'signature' => 'hussain-moradi-pur.png',
                'email' => 'hussain@email.com',
                'user_type_id' => UserTypeEnum::Teacher,
            ]);
        }

        $this->call(PermissionSeeder::class);

        // Seeders
        $this->call(CountrySeeder::class);
        $this->call(YearSeeder::class);
        $this->call(MonthSeeder::class);
        $this->call(GradeSeeder::class);
        $this->call(SubGradeSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(ResultSeeder::class);
        $this->call(ResultTypeSeeder::class);
        $this->call(GradeSubjectTableSeeder::class);
    }
}
