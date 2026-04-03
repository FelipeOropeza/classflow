<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Diretor (Admin)
        User::factory()->create([
            'name' => 'Diretor Geral',
            'email' => 'diretor@classflow.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        // 2. Ano Letivo
        $year = AcademicYear::create([
            'name' => '2026',
            'active' => true,
        ]);

        // 3. Bimestres
        $terms = ['1º Bimestre', '2º Bimestre', '3º Bimestre', '4º Bimestre'];
        foreach ($terms as $name) {
            Term::create([
                'name' => $name,
                'academic_year_id' => $year->id,
            ]);
        }

        // 4. Disciplinas
        $subjects = [
            'Matemática', 'Português', 'Ciências', 'História', 
            'Geografia', 'Artes', 'Educação Física', 'Inglês'
        ];
        foreach ($subjects as $name) {
            Subject::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
}
