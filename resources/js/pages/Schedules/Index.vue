<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router, usePage, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
    Play, 
    Plus,
    Clock, 
    User as UserIcon, 
    BookOpen,
    Sparkles,
    AlertCircle,
    FileDown
} from 'lucide-vue-next';

// -------------------------------------------------------------------------
// PROPS & TYPES
// -------------------------------------------------------------------------

const props = defineProps<{
    schoolClass: {
        id: number;
        name: string;
        academic_year_id: number;
    };
    schedules: Array<{
        id: number;
        class_subject_id: number;
        day_of_week: number;
        period: number;
        class_subject: {
            id: number;
            subject: { name: string };
            teacher: { name: string };
        };
    }>;
    classSubjects: Array<{
        id: number;
        subject: { name: string };
        teacher: { name: string };
    }>;
    errors: Record<string, string>;
}>();

// -------------------------------------------------------------------------
// STATE & COMPUTED
// -------------------------------------------------------------------------

const page = usePage();
const isAdmin = computed(() => {
    const auth = page.props.auth as any;
    return ['admin', 'director'].includes(auth?.user?.role);
});

const days = [
    { id: 1, name: 'Segunda-feira', short: 'SEG' },
    { id: 2, name: 'Terça-feira', short: 'TER' },
    { id: 3, name: 'Quarta-feira', short: 'QUA' },
    { id: 4, name: 'Quinta-feira', short: 'QUI' },
    { id: 5, name: 'Sexta-feira', short: 'SEX' },
];

const periods = [1, 2, 3, 4, 5, 6];

const getSchedule = (day: number, period: number) => {
    return props.schedules.find(s => s.day_of_week === day && s.period === period);
};

// -------------------------------------------------------------------------
// ACTIONS
// -------------------------------------------------------------------------

const manualForm = useForm({
    class_subject_id: null as number | null,
    day_of_week: null as number | null,
    period: null as number | null
});

const activeCell = ref<{ day: number | null, period: number | null }>({ day: null, period: null });

const openManualAssign = (day: number, period: number) => {
    if (!isAdmin.value) return;
    activeCell.value = { day, period };
};

const assignSchedule = (day: number, period: number) => {
    manualForm.day_of_week = day;
    manualForm.period = period;
    
    manualForm.post(route('classes.schedules.store', props.schoolClass.id), {
        preserveScroll: true,
        onSuccess: () => {
            activeCell.value = { day: null, period: null };
            manualForm.reset();
        }
    });
};

const isGenerating = ref(false);
const generateAuto = () => {
    if (confirm('Deseja gerar o quadro de horários automaticamente? Isso irá substituir as aulas atuais desta turma.')) {
        isGenerating.value = true;
        router.post(route('classes.schedules.generate', props.schoolClass.id), {}, {
            onFinish: () => isGenerating.value = false
        });
    }
};

const getSubjectColor = (name: string) => {
    const lower = name.toLowerCase();
    if (lower.includes('matem')) return 'bg-rose-50 text-rose-700 border-rose-100 shadow-rose-100/50';
    if (lower.includes('portug')) return 'bg-indigo-50 text-indigo-700 border-indigo-100 shadow-indigo-100/50';
    if (lower.includes('hist')) return 'bg-amber-50 text-amber-700 border-amber-100 shadow-amber-100/50';
    if (lower.includes('cienc')) return 'bg-emerald-50 text-emerald-700 border-emerald-100 shadow-emerald-100/50';
    if (lower.includes('geog')) return 'bg-sky-50 text-sky-700 border-sky-100 shadow-sky-100/50';
    if (lower.includes('artes')) return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-100 shadow-fuchsia-100/50';
    return 'bg-slate-50 text-slate-700 border-slate-100 shadow-slate-100/50';
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="`Horários — ${schoolClass.name}`" />

        <div class="min-h-screen bg-slate-50 py-12">
            <div class="max-w-[1400px] mx-auto px-6">
                
                <!-- LIGHT CLEAN HEADER -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 mb-2">
                             <div class="p-1.5 bg-indigo-600 rounded-lg text-white">
                                <Clock :size="18" />
                             </div>
                             <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Escala de Aulas</span>
                        </div>
                        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                            Horário — <span class="text-indigo-600">{{ schoolClass.name }}</span>
                        </h1>
                        <p class="text-slate-500 font-medium">Gerenciamento dinâmico de aulas semanais.</p>
                    </div>

                    <div v-if="isAdmin" class="flex items-center gap-3">
                        <a 
                            :href="route('classes.schedules.pdf', schoolClass.id)"
                            target="_blank"
                            class="inline-flex items-center justify-center rounded-2xl bg-white border border-slate-200 px-5 py-4 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all active:scale-95"
                        >
                            <FileDown :size="18" class="mr-2 text-slate-500" />
                            Exportar PDF
                        </a>
                        <button 
                            @click="generateAuto" 
                            :disabled="isGenerating"
                            class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-slate-200 hover:bg-slate-800 transition-all active:scale-95 disabled:opacity-50"
                        >
                            <Sparkles v-if="!isGenerating" :size="18" class="mr-2" />
                            <span v-else class="animate-spin border-2 border-white/20 border-t-white rounded-full w-4 h-4 mr-2"></span>
                            {{ isGenerating ? 'Gerando...' : 'Gerar Automático' }}
                        </button>
                    </div>
                </div>

                <!-- ERROR ALERT -->
                <div v-if="Object.keys(errors).length > 0" class="mb-10 animate-in fade-in slide-in-from-top-4">
                    <div class="rounded-3xl border-2 border-rose-100 bg-rose-50/50 p-6 flex gap-4 items-start">
                        <AlertCircle class="text-rose-500 shrink-0 mt-1" :size="20" />
                        <div>
                             <h3 class="text-sm font-black text-rose-800 uppercase tracking-widest mb-1">Erro de Validação</h3>
                             <ul class="text-sm text-rose-700 font-medium list-disc ml-4 space-y-1">
                                <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
                             </ul>
                        </div>
                    </div>
                </div>

                <!-- WHITE STYLE TIMETABLE GRID -->
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="py-8 px-6 text-center w-36">
                                        <div class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Período</div>
                                    </th>
                                    <th v-for="day in days" :key="day.id" class="px-6 py-8 text-center border-l border-slate-50">
                                        <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">{{ day.short }}</div>
                                        <div class="text-lg font-black text-slate-900">{{ day.name }}</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-for="period in periods" :key="period" class="group">
                                    <!-- Sidebar Period -->
                                    <td class="py-12 px-6 text-center bg-slate-50/20">
                                        <div class="flex flex-col items-center">
                                            <span class="text-3xl font-black text-slate-900 leading-none mb-1">{{ period }}º</span>
                                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Aula</span>
                                        </div>
                                    </td>

                                    <!-- Content Cell -->
                                    <td v-for="day in days" :key="day.id" class="p-4 w-[18%] border-l border-slate-50 group-hover:bg-slate-50/30 transition-colors">
                                        
                                        <!-- ALLOCATED CARD: LIGHT STYLE -->
                                        <div v-if="getSchedule(day.id, period)" 
                                            class="h-full min-h-[140px] rounded-[2rem] border p-6 flex flex-col justify-between transition-all hover:scale-[1.03] hover:shadow-2xl shadow-sm"
                                            :class="getSubjectColor(getSchedule(day.id, period)!.class_subject.subject.name)"
                                        >
                                            <div class="flex justify-between items-start">
                                                <div class="h-10 w-10 rounded-2xl bg-white/60 flex items-center justify-center shadow-sm">
                                                    <BookOpen :size="18" />
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-1">
                                                <h4 class="text-lg font-black tracking-tight leading-none mb-2">
                                                    {{ getSchedule(day.id, period)?.class_subject.subject.name }}
                                                </h4>
                                                <div class="flex items-center gap-2 text-xs font-bold opacity-70">
                                                    <UserIcon :size="14" />
                                                    <span class="truncate">{{ getSchedule(day.id, period)?.class_subject.teacher.name }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- EMPTY: PURE WHITE CLEAN STYLE -->
                                        <div v-else class="h-full min-h-[140px] flex items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-100 bg-white group-hover:border-indigo-200 transition-all relative">
                                            
                                            <!-- Manual Selector UI -->
                                            <div v-if="activeCell.day === day.id && activeCell.period === period" class="w-full p-4 z-10 animate-in zoom-in-95">
                                                <div class="bg-white rounded-[1.5rem] shadow-2xl p-4 border border-slate-50">
                                                    <select 
                                                        v-model="manualForm.class_subject_id" 
                                                        @change="assignSchedule(day.id, period)"
                                                        class="w-full rounded-xl border-slate-100 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-indigo-600 transition-all outline-none"
                                                    >
                                                        <option :value="null" disabled>Escolher Aula...</option>
                                                        <option v-for="cs in classSubjects" :key="cs.id" :value="cs.id">
                                                            {{ cs.subject.name }} — {{ cs.teacher.name }}
                                                        </option>
                                                    </select>
                                                    <button @click.stop="activeCell = {day: null, period: null}" class="mt-3 w-full text-[10px] font-black uppercase text-slate-400 hover:text-indigo-600 transition-colors">
                                                        Fechar
                                                    </button>
                                                </div>
                                            </div>

                                            <button v-else-if="isAdmin" @click="openManualAssign(day.id, period)" class="p-4 flex flex-col items-center gap-3 text-slate-200 hover:text-indigo-600 transition-all group/btn">
                                                <div class="h-12 w-12 rounded-full border-2 border-slate-50 flex items-center justify-center group-hover/btn:border-indigo-100 group-hover/btn:bg-indigo-50 transition-all shadow-sm">
                                                    <Plus :size="24" />
                                                </div>
                                                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Alocar</span>
                                            </button>

                                            <span v-else class="text-[10px] font-black text-slate-100 uppercase tracking-widest tracking-[0.3em]">---</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SIMPLE LEGEND -->
                <div class="mt-12 flex flex-wrap justify-center gap-8 px-6">
                    <div v-for="sub in ['Matemática', 'Português', 'História', 'Ciências']" :key="sub" class="flex items-center gap-3">
                         <div class="w-4 h-4 rounded-full border-2 shadow-sm" :class="getSubjectColor(sub)"></div>
                         <span class="text-xs font-bold text-slate-500">{{ sub }}</span>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
