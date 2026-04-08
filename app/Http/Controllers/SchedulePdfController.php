<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;

class SchedulePdfController extends Controller
{
    public function export(SchoolClass $schoolClass)
    {
        /** @var \App\Models\User $user */
        $user = request()->user();
        abort_unless($user && $user->isAdmin(), 403);

        $schedules = Schedule::with(['classSubject.subject', 'classSubject.teacher'])
            ->whereHas('classSubject', fn ($q) => $q->where('class_id', $schoolClass->id))
            ->get();

        $days = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
        ];

        $periodTimes = [
            1 => '07:00 - 07:50',
            2 => '07:50 - 08:40',
            3 => '09:00 - 09:50',
            4 => '09:50 - 10:40',
            5 => '10:40 - 11:30',
            6 => '11:30 - 12:20',
        ];

        // Organiza em grid[period][day] para facilitar a view
        $grid = [];
        for ($period = 1; $period <= 6; $period++) {
            $grid[$period] = [];
            foreach (array_keys($days) as $day) {
                $grid[$period][$day] = $schedules->first(
                    fn ($s) => $s->period === $period && $s->day_of_week === $day
                );
            }
        }

        $pdf = Pdf::loadView('pdf.schedule', [
            'schoolClass' => $schoolClass,
            'grid'        => $grid,
            'days'        => $days,
            'periodTimes' => $periodTimes,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("horario-{$schoolClass->name}.pdf");
    }

    public function exportAll()
    {
        /** @var \App\Models\User $user */
        $user = request()->user();
        abort_unless($user && $user->isAdmin(), 403);

        $classes = SchoolClass::with(['classSubjects.subject', 'classSubjects.teacher'])
            ->orderBy('name')
            ->get();

        $days = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
        ];

        $periodTimes = [
            1 => '07:00 - 07:50',
            2 => '07:50 - 08:40',
            3 => '09:00 - 09:50',
            4 => '09:50 - 10:40',
            5 => '10:40 - 11:30',
            6 => '11:30 - 12:20',
        ];

        $allData = [];

        foreach ($classes as $schoolClass) {
            $schedules = Schedule::with(['classSubject.subject', 'classSubject.teacher'])
                ->whereHas('classSubject', fn ($q) => $q->where('class_id', $schoolClass->id))
                ->get();

            $grid = [];
            for ($period = 1; $period <= 6; $period++) {
                $grid[$period] = [];
                foreach (array_keys($days) as $day) {
                    $grid[$period][$day] = $schedules->first(
                        fn ($s) => $s->period === $period && $s->day_of_week === $day
                    );
                }
            }

            $allData[] = [
                'schoolClass' => $schoolClass,
                'grid' => $grid,
            ];
        }

        $pdf = Pdf::loadView('pdf.all_schedules', [
            'allData' => $allData,
            'days'    => $days,
            'periodTimes' => $periodTimes,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("todos-os-horarios.pdf");
    }
}
