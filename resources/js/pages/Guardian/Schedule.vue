<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Clock, BookOpen, User as UserIcon, GraduationCap } from 'lucide-vue-next';

// -------------------------------------------------------------------------
// TYPES & PROPS
// -------------------------------------------------------------------------

interface ScheduleEntry {
    id: number;
    day_of_week: number;
    period: number;
    class_subject: {
        subject: { name: string };
        teacher: { name: string };
    };
}

interface Day {
    id: number;
    name: string;
    short: string;
}

const props = defineProps<{
    student?: { id: number; name: string };
    schoolClass?: { id: number; name: string };
    schedules: ScheduleEntry[];
    days: Day[];
}>();

// -------------------------------------------------------------------------
// COMPUTED
// -------------------------------------------------------------------------

const periods = [1, 2, 3, 4, 5, 6];

const getSlot = (day: number, period: number) =>
    props.schedules.find(s => s.day_of_week === day && s.period === period);

const uniqueSubjects = computed(() => {
    const seen = new Set<string>();
    return props.schedules
        .map(s => s.class_subject.subject.name)
        .filter(name => { const ok = !seen.has(name); seen.add(name); return ok; });
});

const getSubjectColor = (name: string = '') => {
    const lower = name.toLowerCase();
    if (lower.includes('matem')) return 'bg-rose-50 text-rose-700 border-rose-200 shadow-rose-100/50';
    if (lower.includes('portug')) return 'bg-indigo-50 text-indigo-700 border-indigo-200 shadow-indigo-100/50';
    if (lower.includes('hist')) return 'bg-amber-50 text-amber-700 border-amber-200 shadow-amber-100/50';
    if (lower.includes('cienc')) return 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-emerald-100/50';
    if (lower.includes('geog')) return 'bg-sky-50 text-sky-700 border-sky-200 shadow-sky-100/50';
    if (lower.includes('artes')) return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200 shadow-fuchsia-100/50';
    return 'bg-slate-50 text-slate-600 border-slate-200 shadow-slate-100/50';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Grade de Horários" />

        <div class="min-h-screen bg-slate-50 py-12">
            <div class="max-w-[1400px] mx-auto px-6 space-y-8">

                <!-- HEADER: CLEAN AND FRIENDLY FOR PARENTS -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="p-2 bg-indigo-600 rounded-xl text-white">
                                <GraduationCap :size="20" />
                            </div>
                            <span class="text-[11px] font-black uppercase tracking-[0.25em] text-slate-400">Grade de Aulas</span>
                        </div>
                        <h1 class="text-4xl font-extrabold text-slate-900 leading-tight">
                            Horários de
                            <span class="text-indigo-600">{{ student?.name }}</span>
                        </h1>
                        <p class="text-slate-500 font-medium mt-1" v-if="schoolClass">
                            Turma: <strong class="text-slate-700">{{ schoolClass.name }}</strong>
                        </p>
                    </div>
                </div>

                <!-- NO DATA STATE -->
                <div v-if="!schoolClass || schedules.length === 0" class="bg-white rounded-3xl border-2 border-dashed border-slate-100 p-20 text-center">
                    <Clock :size="48" class="mx-auto text-slate-200 mb-4" />
                    <h3 class="text-xl font-black text-slate-400">Grade ainda não disponível</h3>
                    <p class="text-slate-300 text-sm mt-2">O coordenador ainda não publicou o quadro de horários. Volte em breve.</p>
                </div>

                <!-- FULL SCHEDULE TABLE: SIMPLE & READABLE -->
                <div v-else class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50/60 border-b border-slate-100">
                                    <th class="py-8 px-8 text-[10px] font-black uppercase tracking-[0.25em] text-slate-300 w-36 text-left">
                                        Período
                                    </th>
                                    <th v-for="day in days" :key="day.id" class="px-5 py-8 text-center border-l border-slate-100">
                                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">{{ day.short }}</div>
                                        <div class="text-base font-black text-slate-900">{{ day.name }}</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="period in periods" :key="period" class="group hover:bg-slate-50/40 transition-colors">
                                    <!-- Period Label -->
                                    <td class="py-10 px-8 bg-slate-50/20 border-r border-slate-50">
                                        <div class="flex flex-col">
                                            <span class="text-3xl font-black text-slate-900 leading-none">{{ period }}º</span>
                                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest mt-1">Horário</span>
                                        </div>
                                    </td>

                                    <!-- Content Cell: Simple readable card -->
                                    <td v-for="day in days" :key="day.id" class="p-4 border-l border-slate-50">
                                        <!-- Lesson card -->
                                        <div v-if="getSlot(day.id, period)"
                                            class="rounded-2xl border p-5 shadow-sm min-h-[120px] flex flex-col justify-between"
                                            :class="getSubjectColor(getSlot(day.id, period)?.class_subject.subject.name)"
                                        >
                                            <div class="h-9 w-9 rounded-2xl bg-white/60 flex items-center justify-center mb-4 shadow-sm">
                                                <BookOpen :size="16" />
                                            </div>

                                            <div class="space-y-1">
                                                <h4 class="text-base font-black tracking-tight leading-tight">
                                                    {{ getSlot(day.id, period)?.class_subject.subject.name }}
                                                </h4>
                                                <div class="flex items-center gap-1.5 text-xs font-bold opacity-70">
                                                    <UserIcon :size="13" />
                                                    <span class="truncate">{{ getSlot(day.id, period)?.class_subject.teacher.name }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Empty slot -->
                                        <div v-else class="min-h-[120px] rounded-2xl border-2 border-dashed border-slate-50 flex items-center justify-center">
                                            <span class="text-[10px] font-black text-slate-100 uppercase tracking-widest">—</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SUBJECT LEGEND: VISUAL KEY FOR PARENTS -->
                <div v-if="uniqueSubjects.length > 0" class="bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300 mb-5">Legenda das Disciplinas</p>
                    <div class="flex flex-wrap gap-4">
                        <div v-for="sub in uniqueSubjects" :key="sub" class="flex items-center gap-3 bg-slate-50 rounded-2xl px-4 py-3 border border-slate-100">
                            <div class="h-3 w-3 rounded-full border shadow-sm" :class="getSubjectColor(sub)"></div>
                            <span class="text-sm font-bold text-slate-700">{{ sub }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
