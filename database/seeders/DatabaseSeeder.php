<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\ClassSubject;
use App\Models\SchoolEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Criar Ano Letivo Ativo
        $year = AcademicYear::create([
            'name' => '2026',
            'active' => true,
        ]);

        // 1.1 Criar Bimestres
        $terms = [
            ['name' => '1º Bimestre', 'start_date' => '2026-02-01', 'end_date' => '2026-04-30'],
            ['name' => '2º Bimestre', 'start_date' => '2026-05-01', 'end_date' => '2026-07-31'],
            ['name' => '3º Bimestre', 'start_date' => '2026-08-01', 'end_date' => '2026-10-31'],
            ['name' => '4º Bimestre', 'start_date' => '2026-11-01', 'end_date' => '2026-12-20'],
        ];

        foreach ($terms as $term) {
            \App\Models\Term::create(array_merge($term, [
                'academic_year_id' => $year->id,
            ]));
        }

        // 2. Criar Usuários de Teste (Admin, Professor, Responsável)
        User::create([
            'name' => 'Admin ClassFlow',
            'email' => 'admin@classflow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $teacher = User::create([
            'name' => 'Prof. Ricardo Oliveira',
            'email' => 'professor@classflow.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $guardian = User::create([
            'name' => 'Maria Silva (Responsável)',
            'email' => 'pais@classflow.com',
            'password' => Hash::make('password'),
            'role' => 'guardian',
        ]);

        // 3. Criar Disciplina e Turma
        $math = Subject::create([
            'name' => 'Matemática', 
            'slug' => 'matematica'
        ]);
        
        Subject::create([
            'name' => 'Português', 
            'slug' => 'portugues'
        ]);

        $classA = SchoolClass::create([
            'name' => '1º Ano A',
            'academic_year_id' => $year->id,
        ]);

        // 4. Vincular Professor à Turma/Disciplina
        ClassSubject::create([
            'class_id' => $classA->id,
            'subject_id' => $math->id,
            'teacher_id' => $teacher->id,
        ]);

        // 5. Criar Alunos e Matricular
        $students = [
            ['name' => 'Ana Beatriz Silva', 'registration_number' => '2026001', 'birth_date' => '2019-05-15'],
            ['name' => 'Bruno Fernandes', 'registration_number' => '2026002', 'birth_date' => '2019-08-20'],
            ['name' => 'Carla Souza', 'registration_number' => '2026003', 'birth_date' => '2019-11-10'],
            ['name' => 'Daniel Lima', 'registration_number' => '2026004', 'birth_date' => '2019-02-28'],
            ['name' => 'Eduarda Santos', 'registration_number' => '2026005', 'birth_date' => '2019-06-05'],
        ];

        foreach ($students as $data) {
            $student = Student::create(array_merge($data, [
                'guardian_id' => $guardian->id
            ]));
            
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $classA->id,
                'academic_year_id' => $year->id,
            ]);
        }
        // School Events/Agenda
        SchoolEvent::updateOrCreate(
            ['academic_year_id' => $year->id, 'title' => 'Conselho de Classe'],
            ['event_date' => '2026-04-15', 'type' => 'meeting', 'description' => 'Turno Matutino - 09:00h']
        );

        SchoolEvent::updateOrCreate(
            ['academic_year_id' => $year->id, 'title' => 'Feriado Nacional'],
            ['event_date' => '2026-04-21', 'type' => 'holiday', 'description' => 'Tiradentes (Recesso)']
        );
    }
}
