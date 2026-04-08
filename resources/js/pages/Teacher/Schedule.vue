<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Clock, BookOpen, User as UserIcon, School } from 'lucide-vue-next';

// -------------------------------------------------------------------------
// TYPES & PROPS
// -------------------------------------------------------------------------

interface ScheduleEntry {
    id: number;
    class_subject_id: number;
    day_of_week: number;
    period: number;
    class_subject: {
        subject: { name: string };
        school_class: { id: number; name: string };
    };
}

interface SchoolClass {
    id: number;
    name: string;
    is_active: boolean;
}

interface Day {
    id: number;
    name: string;
    short: string;
}

const props = defineProps<{
    teacher: { id: number; name: string; email: string };
    schedules: ScheduleEntry[];
    classes: SchoolClass[];
    days: Day[];
}>();

// -------------------------------------------------------------------------
// COMPUTED
// -------------------------------------------------------------------------

const periods = [1, 2, 3, 4, 5, 6];

const getSlot = (classId: number, day: number, period: number) =>
    props.schedules.find(
        s => s.class_subject.school_class?.id === classId
          && s.day_of_week === day
          && s.period === period
    );

const totalLessons = computed(() => props.schedules.length);

const getSubjectColor = (name: string = '') => {
    const lower = name.toLowerCase();
    if (lower.includes('matem')) return 'bg-rose-50 text-rose-700 border-rose-200';
    if (lower.includes('portug')) return 'bg-indigo-50 text-indigo-700 border-indigo-200';
    if (lower.includes('hist')) return 'bg-amber-50 text-amber-700 border-amber-200';
    if (lower.includes('cienc')) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    if (lower.includes('geog')) return 'bg-sky-50 text-sky-700 border-sky-200';
    if (lower.includes('artes')) return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200';
    return 'bg-slate-50 text-slate-600 border-slate-200';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Meu Horário" />

        <div class="min-h-screen bg-slate-50 py-12">
            <div class="max-w-[1400px] mx-auto px-6 space-y-10">

                <!-- HEADER -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2 bg-indigo-600 rounded-xl text-white">
                                <Clock :size="20" />
                            </div>
                            <span class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400">Minha Grade</span>
                        </div>
                        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Meu Horário</h1>
                        <p class="text-slate-500 font-medium mt-1">{{ teacher.name }}</p>
                    </div>

                    <div class="flex gap-4">
                        <div class="bg-white rounded-2xl px-6 py-4 border border-slate-100 shadow-sm text-center">
                            <div class="text-3xl font-black text-slate-900">{{ totalLessons }}</div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Aulas/Semana</div>
                        </div>
                        <div class="bg-white rounded-2xl px-6 py-4 border border-slate-100 shadow-sm text-center">
                            <div class="text-3xl font-black text-slate-900">{{ classes.length }}</div>
                            <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Turmas</div>
                        </div>
                    </div>
                </div>

                <!-- NO SCHEDULE STATE -->
                <div v-if="schedules.length === 0" class="bg-white rounded-3xl border-2 border-dashed border-slate-100 p-20 text-center">
                    <Clock :size="48" class="mx-auto text-slate-200 mb-4" />
                    <h3 class="text-xl font-black text-slate-400">Nenhum horário alocado</h3>
                    <p class="text-slate-300 text-sm mt-2">Aguarde o coordenador gerar o quadro de horários.</p>
                </div>

                <!-- ONE TABLE PER CLASS -->
                <div v-for="schoolClass in classes" :key="schoolClass.id" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden">
                    <!-- Class Header -->
                    <div class="flex items-center gap-3 px-8 py-6 border-b border-slate-50">
                        <div class="p-2 bg-slate-100 rounded-xl text-slate-600">
                            <School :size="18" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-black text-slate-900">{{ schoolClass.name }}</h2>
                                <span v-if="!schoolClass.is_active" class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[10px] font-black uppercase rounded-lg">Inativa</span>
                            </div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Grade Semanal</p>
                        </div>
                    </div>

                    <!-- Timetable Grid -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b border-slate-50">
                                    <th class="py-5 px-6 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 w-28 text-center">Período</th>
                                    <th v-for="day in days" :key="day.id" class="px-5 py-5 text-center border-l border-slate-50">
                                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ day.short }}</div>
                                        <div class="text-sm font-black text-slate-700 mt-0.5">{{ day.name }}</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="period in periods" :key="period" class="group">
                                    <td class="py-6 px-6 text-center bg-slate-50/30">
                                        <span class="text-2xl font-black text-slate-700 leading-none">{{ period }}º</span>
                                    </td>
                                    <td v-for="day in days" :key="day.id" class="p-3 border-l border-slate-50 group-hover:bg-slate-50/40 transition-colors">
                                        <!-- Allocated -->
                                        <div v-if="getSlot(schoolClass.id, day.id, period)"
                                            class="rounded-2xl border p-4 shadow-sm transition-all hover:scale-[1.03] hover:shadow-lg min-h-[90px] flex flex-col justify-between"
                                            :class="getSubjectColor(getSlot(schoolClass.id, day.id, period)?.class_subject.subject.name)"
                                        >
                                            <div class="h-8 w-8 rounded-xl bg-white/60 flex items-center justify-center shadow-sm mb-3">
                                                <BookOpen :size="14" />
                                            </div>
                                            <div class="text-sm font-black tracking-tight leading-tight">
                                                {{ getSlot(schoolClass.id, day.id, period)?.class_subject.subject.name }}
                                            </div>
                                        </div>

                                        <!-- Empty -->
                                        <div v-else class="min-h-[90px] rounded-2xl border-2 border-dashed border-slate-50 bg-white flex items-center justify-center">
                                            <span class="text-[10px] font-black text-slate-100 uppercase tracking-[0.2em]">—</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
