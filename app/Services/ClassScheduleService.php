<?php

namespace App\Services;

use App\Models\ClassSubject;
use App\Models\Schedule;
use Exception;
use Illuminate\Support\Facades\DB;

class ClassScheduleService
{
    /**
     * Retorna o limite máximo de aulas semanais por disciplina.
     * Matemática e Português: 6 aulas. Demais: 4 aulas.
     */
    public function getWeeklyLimit(string $subjectName): int
    {
        $lower = strtolower($subjectName);
        return (str_contains($lower, 'matem') || str_contains($lower, 'portug')) ? 6 : 4;
    }

    /**
     * Conta quantas aulas a disciplina já tem alocadas na semana para a turma.
     */
    public function countWeeklyLessons(int $classSubjectId): int
    {
        return Schedule::where('class_subject_id', $classSubjectId)->count();
    }

    /**
     * Verifica se o professor já possui aula neste mesmo dia e período em qualquer turma.
     */
    public function isTeacherAvailable(int $teacherId, int $dayOfWeek, int $period): bool
    {
        return !Schedule::where('day_of_week', $dayOfWeek)
            ->where('period', $period)
            ->whereHas('classSubject', fn ($q) => $q->where('teacher_id', $teacherId))
            ->exists();
    }

    /**
     * Aloca uma aula manualmente, validando:
     * 1) Choque de professor entre turmas
     * 2) Choque de horário da turma
     * 3) Teto de aulas semanais por disciplina
     */
    public function allocateManual(int $classSubjectId, int $dayOfWeek, int $period): Schedule
    {
        $classSubject = ClassSubject::with('subject')->findOrFail($classSubjectId);

        // Valida choque de professor
        if (!$this->isTeacherAvailable($classSubject->teacher_id, $dayOfWeek, $period)) {
            throw new Exception("Conflito: o professor já possui aula neste horário em outra turma.");
        }

        // Valida choque de horário da turma
        $classOccupied = Schedule::where('day_of_week', $dayOfWeek)
            ->where('period', $period)
            ->whereHas('classSubject', fn ($q) => $q->where('class_id', $classSubject->class_id))
            ->exists();

        if ($classOccupied) {
            throw new Exception("Conflito: a turma já possui outra aula neste horário.");
        }

        // Valida teto de aulas semanais
        $limit = $this->getWeeklyLimit($classSubject->subject->name ?? '');
        $current = $this->countWeeklyLessons($classSubjectId);

        if ($current >= $limit) {
            throw new Exception("Limite atingido: {$classSubject->subject->name} já possui {$limit} aulas alocadas nesta semana.");
        }

        return Schedule::create([
            'class_subject_id' => $classSubjectId,
            'day_of_week'      => $dayOfWeek,
            'period'           => $period,
        ]);
    }

    /**
     * Gera automaticamente o quadro de horários para uma turma.
     *
     * Estratégia de distribuição equilibrada:
     * - Cria uma fila de "tokens" para cada disciplina conforme seu teto semanal.
     * - Embaralha a fila para evitar que a mesma disciplina ocupe sempre os primeiros slots.
     * - Itera pelos dias/períodos e aloca o próximo token da fila que não cause conflitos.
     *
     * Math/Port = 6 aulas/semana | Demais = 4 aulas/semana
     */
    public function generateAutomaticSchedule(int $classId): void
    {
        $classSubjects = ClassSubject::with('subject')->where('class_id', $classId)->get();

        if ($classSubjects->isEmpty()) {
            throw new Exception("Nenhuma disciplina vinculada a esta turma. Cadastre as disciplinas primeiro.");
        }

        $periodsPerDay = 6; // 5 dias × 6 períodos = 30 slots (cobre até 28 aulas)
        $daysOfWeek    = [1, 2, 3, 4, 5];

        // Calcula total de aulas solicitadas vs slots disponíveis
        $totalSlots    = count($daysOfWeek) * $periodsPerDay;
        $totalRequired = $classSubjects->sum(fn ($cs) => $this->getWeeklyLimit($cs->subject->name ?? ''));

        if ($totalRequired > $totalSlots) {
            throw new Exception("Erro de configuração: total de aulas ({$totalRequired}) ultrapassa os slots disponíveis ({$totalSlots}/semana).");
        }

        DB::beginTransaction();
        try {
            // Limpa horários existentes da turma
            Schedule::whereHas('classSubject', fn ($q) => $q->where('class_id', $classId))->delete();

            // Monta fila de tokens: cada disciplina aparece N vezes (seu limite)
            $queue = [];
            foreach ($classSubjects as $cs) {
                $limit = $this->getWeeklyLimit($cs->subject->name ?? '');
                for ($i = 0; $i < $limit; $i++) {
                    $queue[] = [
                        'class_subject_id' => $cs->id,
                        'teacher_id'       => $cs->teacher_id,
                    ];
                }
            }

            // Embaralha a fila para distribuição variada
            shuffle($queue);

            // Distribui: para cada slot do calendário, tenta encaixar o próximo token da fila
            foreach ($daysOfWeek as $day) {
                for ($period = 1; $period <= $periodsPerDay; $period++) {
                    // Percorre a fila procurando um token que não cause conflito
                    foreach ($queue as $idx => $token) {
                        if ($this->isTeacherAvailable($token['teacher_id'], $day, $period)) {
                            Schedule::create([
                                'class_subject_id' => $token['class_subject_id'],
                                'day_of_week'      => $day,
                                'period'           => $period,
                            ]);
                            // Remove token já usado da fila
                            array_splice($queue, $idx, 1);
                            break; // slot preenchido, vai pro próximo
                        }
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
