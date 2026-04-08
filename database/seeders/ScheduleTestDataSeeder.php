<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ScheduleTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Academic Year
        $year = AcademicYear::updateOrCreate(
            ['name' => '2026'],
            [
                'active' => true
            ]
        );

        // 2. Create Subjects
        $subjectsData = [
            'Matemática',
            'Português',
            'História',
            'Ciências',
            'Geografia',
            'Artes',
        ];

        $subjectMap = [];
        foreach ($subjectsData as $name) {
            $subjectMap[$name] = Subject::updateOrCreate(
                ['name' => $name],
                ['slug' => \Illuminate\Support\Str::slug($name)]
            )->id;
        }

        // 3. Create Teachers
        $teacherNames = [
            'Ricardo Oliveira', 
            'Maria Silva', 
            'João Souza', 
            'Ana Paula', 
            'Carlos Ferreira'
        ];
        
        $teacherIds = [];
        foreach ($teacherNames as $index => $name) {
            $teacherIds[] = User::updateOrCreate(
                ['email' => 'teacher' . ($index + 1) . '@classflow.test'],
                [
                    'name' => 'Prof. ' . $name,
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                ]
            )->id;
        }

        // 4. Create Classes
        $classNames = ['6º Ano A', '6º Ano B'];
        $classIds = [];
        foreach ($classNames as $name) {
            $classIds[] = SchoolClass::updateOrCreate(['name' => $name, 'academic_year_id' => $year->id])->id;
        }

        // 5. Link Subjects to Classes (Academic Links)
        // class_subject table
        foreach ($classIds as $classId) {
            foreach ($subjectMap as $name => $subjectId) {
                // Find a teacher to assign
                $teacherId = $teacherIds[array_rand($teacherIds)];

                DB::table('class_subject')->updateOrInsert(
                    ['class_id' => $classId, 'subject_id' => $subjectId],
                    ['teacher_id' => $teacherId, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
