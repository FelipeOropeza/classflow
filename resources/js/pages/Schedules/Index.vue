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
    FileDown,
    Trash2,
    X
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

const getWeeklyLimit = (name: string) => {
    const lower = name.toLowerCase();
    return (lower.includes('matem') || lower.includes('portug')) ? 6 : 4;
};

const allocationStats = computed(() => {
    return props.classSubjects.map(cs => {
        const count = props.schedules.filter(s => s.class_subject_id === cs.id).length;
        const limit = getWeeklyLimit(cs.subject.name);
        return {
            ...cs,
            count,
            limit,
            isFull: count >= limit
        };
    });
});

const totalAllocated = computed(() => props.schedules.length);
const totalRequired = computed(() => allocationStats.value.reduce((acc, curr) => acc + curr.limit, 0));

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

const deleteSchedule = (id: number) => {
    if (confirm('Remover esta aula do quadro de horários?')) {
        router.delete(route('classes.schedules.destroy', id), {
            preserveScroll: true
        });
    }
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
    if (lower.includes('matem')) return 'border-l-rose-500 bg-rose-50/30 text-rose-800';
    if (lower.includes('portug')) return 'border-l-indigo-500 bg-indigo-50/30 text-indigo-800';
    if (lower.includes('hist')) return 'border-l-amber-500 bg-amber-50/30 text-amber-800';
    if (lower.includes('cienc')) return 'border-l-emerald-500 bg-emerald-50/30 text-emerald-800';
    if (lower.includes('geog')) return 'border-l-sky-500 bg-sky-50/30 text-sky-800';
    if (lower.includes('artes')) return 'border-l-fuchsia-500 bg-fuchsia-50/30 text-fuchsia-800';
    return 'border-l-slate-400 bg-slate-50/30 text-slate-800';
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
                            PDF
                        </a>
                        <button 
                            @click="generateAuto" 
                            :disabled="isGenerating"
                            class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-slate-200 hover:bg-slate-800 transition-all active:scale-95 disabled:opacity-50"
                        >
                            <Sparkles v-if="!isGenerating" :size="18" class="mr-2 text-amber-400" />
                            <span v-else class="animate-spin border-2 border-white/20 border-t-white rounded-full w-4 h-4 mr-2"></span>
                            {{ isGenerating ? 'Gerando...' : 'Gerar Automático' }}
                        </button>
                    </div>
                </div>

                <!-- PENDING ALLOCATION BAR -->
                <div v-if="isAdmin" class="mb-10 bg-white rounded-3xl border border-slate-100 p-2 shadow-sm flex flex-col md:flex-row items-stretch gap-2 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 flex flex-col justify-center border-r border-slate-100 min-w-[200px]">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status Geral</span>
                        <div class="flex items-end gap-2">
                            <span class="text-2xl font-black text-slate-900 leading-none">{{ totalAllocated }}</span>
                            <span class="text-xs font-bold text-slate-400 pb-0.5">/ {{ totalRequired }} aulas</span>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center gap-4 px-6 py-2 overflow-x-auto no-scrollbar">
                        <div 
                            v-for="stat in allocationStats" 
                            :key="stat.id"
                            class="flex-shrink-0 group relative"
                        >
                            <div 
                                class="flex items-center gap-3 px-4 py-2.5 rounded-2xl border transition-all"
                                :class="stat.isFull ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-white border-slate-100 text-slate-600'"
                            >
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black leading-none mb-0.5 truncate max-w-[100px]">{{ stat.subject.name }}</span>
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex gap-0.5">
                                            <div 
                                                v-for="i in stat.limit" 
                                                :key="i"
                                                class="w-1.5 h-1.5 rounded-full"
                                                :class="i <= stat.count ? 'bg-current' : 'bg-slate-100'"
                                            ></div>
                                        </div>
                                        <span class="text-[10px] font-black opacity-60">{{ stat.count }}/{{ stat.limit }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Tooltip for teacher name -->
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-slate-900 text-white text-[9px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                {{ stat.teacher.name }}
                            </div>
                        </div>
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

                <!-- WHITE MINIMALIST TABLE -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/40 border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse table-fixed">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="py-6 px-4 w-24">
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Aula</div>
                                    </th>
                                    <th v-for="day in days" :key="day.id" class="px-4 py-6 text-center border-l border-slate-200">
                                        <div class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ day.name }}</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="period in periods" :key="period">
                                    <!-- Period Label -->
                                    <td class="py-8 px-4 text-center bg-slate-50/50 font-black text-slate-900 border-r border-slate-200">
                                        <div class="text-2xl">{{ period }}º</div>
                                    </td>

                                    <!-- Day Cell -->
                                    <td v-for="day in days" :key="day.id" class="p-2 h-32 border-l border-slate-200 hover:bg-slate-50/30 transition-colors relative group">
                                        
                                        <!-- ALLOCATED: MINIMALIST CARD -->
                                        <div v-if="getSchedule(day.id, period)" 
                                            class="w-full h-full rounded-xl border-l-[6px] p-3 flex flex-col justify-between shadow-sm bg-white border border-slate-200 border-l-inherit transition-all"
                                            :class="getSubjectColor(getSchedule(day.id, period)!.class_subject.subject.name)"
                                        >
                                            <div class="flex justify-between items-start gap-1">
                                                <div class="text-sm font-black leading-tight truncate">
                                                    {{ getSchedule(day.id, period)?.class_subject.subject.name }}
                                                </div>
                                                <button 
                                                    v-if="isAdmin"
                                                    @click.stop="deleteSchedule(getSchedule(day.id, period)!.id)"
                                                    class="p-1 px-1.5 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all opacity-0 group-hover:opacity-100"
                                                >
                                                    <Trash2 :size="14" />
                                                </button>
                                            </div>
                                            
                                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500 truncate">
                                                <UserIcon :size="12" class="shrink-0" />
                                                <span class="truncate">{{ getSchedule(day.id, period)?.class_subject.teacher.name }}</span>
                                            </div>
                                        </div>

                                        <!-- EMPTY: MINIMALIST BUTTON -->
                                        <div v-else class="w-full h-full flex items-center justify-center relative">
                                            
                                            <!-- Manual Selector UI -->
                                            <div v-if="activeCell.day === day.id && activeCell.period === period" class="absolute inset-0 bg-white z-20 p-2 flex flex-col justify-center items-center gap-2">
                                                <select 
                                                    v-model="manualForm.class_subject_id" 
                                                    @change="assignSchedule(day.id, period)"
                                                    class="w-full rounded-lg border-slate-200 bg-white px-2 py-2 text-[11px] font-bold text-slate-900 focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                                                >
                                                    <option :value="null" disabled>Selecionar...</option>
                                                    <option v-for="cs in allocationStats" :key="cs.id" :value="cs.id" :disabled="cs.isFull">
                                                        {{ cs.subject.name }} ({{ cs.count }}/{{ cs.limit }})
                                                    </option>
                                                </select>
                                                <button @click.stop="activeCell = {day: null, period: null}" class="text-[10px] font-black uppercase text-slate-400 hover:text-slate-900">
                                                    Cancelar
                                                </button>
                                            </div>

                                            <button v-else-if="isAdmin" @click="openManualAssign(day.id, period)" class="w-full h-full flex items-center justify-center text-slate-200 hover:text-indigo-600 transition-all opacity-0 group-hover:opacity-100 bg-indigo-50/0 hover:bg-indigo-50/30">
                                                <Plus :size="20" />
                                            </button>

                                            <span v-else class="text-[10px] font-black text-slate-100 tracking-widest">—</span>
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
