<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
  ClipboardList, 
  Search, 
  Plus, 
  BookOpen, 
  GraduationCap, 
  BarChart3,
  CalendarDays,
  Target,
  Calendar
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Link {
  id: number;
  school_class: { name: string };
  subject: { name: string };
}

interface Term {
  id: number;
  name: string;
}

interface Assessment {
  id: number;
  name: string;
  max_score: number;
  date: string | null;
  term?: { name: string };
}

const props = defineProps<{
  links: Link[];
  terms: Term[];
  assessments: Assessment[];
  filters: { link_id: string | null; term_id: string | null };
  selectedLink: Link | null;
}>();

const selectedLinkLocal = ref(props.filters.link_id || '');
const selectedTermLocal = ref(props.filters.term_id || '');
const showModal = ref(false);

const form = useForm({
  class_subject_id: props.filters.link_id || '',
  term_id: props.filters.term_id || '',
  name: '',
  max_score: 10,
  date: ''
});

const changeFilters = () => {
  router.get(route('assessments.index'), { 
    link_id: selectedLinkLocal.value,
    term_id: selectedTermLocal.value
   }, {
    preserveState: true,
    replace: true,
  });
  form.class_subject_id = selectedLinkLocal.value;
  form.term_id = selectedTermLocal.value;
};

const openModal = () => {
  if (!selectedLinkLocal.value) return;
  showModal.value = true;
};

const submit = () => {
  form.post(route('assessments.store'), {
    onSuccess: () => {
      showModal.value = false;
      form.reset('name', 'max_score', 'date');
    }
  });
};

const goToGrades = (assessmentId: number) => {
  router.get(route('grades.index'), { 
    link_id: selectedLinkLocal.value,
    assessment_id: assessmentId 
  });
};
</script>

<template>
  <Head title="Planejar Avaliações" />

  <AuthenticatedLayout>
    <template #default>
      <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
              <div class="p-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                <ClipboardList class="text-white" :size="24" />
              </div>
              Planejamento de Avaliações
            </h2>
            <p class="text-slate-500 mt-2 font-medium">Organize o cronograma avaliativo de cada bimestre.</p>
          </div>
          
          <button 
            v-if="selectedLinkLocal"
            @click="openModal"
            class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold py-3.5 px-6 rounded-2xl transition-all shadow-lg shadow-indigo-200 flex items-center justify-center gap-2"
          >
            <Plus :size="20" />
            Nova Avaliação
          </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20 grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1 leading-none">Turma & Disciplina</label>
            <div class="relative">
              <select 
                v-model="selectedLinkLocal"
                @change="changeFilters"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer appearance-none"
              >
                <option value="">Selecione um vínculo...</option>
                <option v-for="link in links" :key="link.id" :value="link.id">
                  {{ link.school_class.name }} - {{ link.subject.name }}
                </option>
              </select>
              <BookOpen class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1 leading-none">Bimestre para Visualizar</label>
            <div class="relative">
              <select 
                v-model="selectedTermLocal"
                @change="changeFilters"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer appearance-none"
              >
                <option value="">Todos os Bimestres</option>
                <option v-for="term in terms" :key="term.id" :value="term.id">
                  {{ term.name }}
                </option>
              </select>
              <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>
        </div>

        <!-- Assessments Grid -->
        <div v-if="selectedLink && assessments.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-in slide-in-from-bottom duration-500">
           <div v-for="assessment in assessments" :key="assessment.id" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all group relative overflow-hidden flex flex-col justify-between">
              <div class="absolute top-0 right-0 w-24 h-24 bg-slate-50 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform duration-500"></div>
              
              <div class="relative">
                 <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                       <BarChart3 :size="20" />
                    </div>
                    <div class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 rounded-lg text-[10px] font-black text-indigo-600 uppercase tracking-widest border border-indigo-100/50">
                       {{ assessment.term?.name }}
                    </div>
                 </div>
                 
                 <h4 class="text-xl font-bold text-slate-900 mb-2 truncate" :title="assessment.name">{{ assessment.name }}</h4>
                 
                 <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-sm text-slate-500 font-medium font-mono">
                       <Target :size="16" class="text-indigo-400" />
                       Valor Máximo: {{ assessment.max_score }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-slate-500 font-medium font-mono">
                       <CalendarDays :size="16" class="text-indigo-400" />
                       Data: {{ assessment.date || 'Não definida' }}
                    </div>
                 </div>
              </div>

              <button 
                @click="goToGrades(assessment.id)"
                class="w-full bg-slate-50 hover:bg-indigo-600 hover:text-white text-indigo-600 border border-indigo-100 font-bold py-3 px-4 rounded-xl transition-all flex items-center justify-center gap-2 mt-4 relative z-10 shadow-sm shadow-indigo-100/10"
              >
                <GraduationCap :size="18" />
                Lançar Notas
              </button>
           </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="selectedLinkLocal" class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center text-center space-y-6 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-slate-400">
            <ClipboardList :size="40" />
          </div>
          <div class="max-w-xs">
            <h2 class="text-xl font-bold text-slate-900">Nenhuma avaliação criada</h2>
            <p class="text-slate-500 mt-2">Clique no botão superior para planejar as provas.</p>
          </div>
        </div>

        <div v-else class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center text-center space-y-6 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-200">
            <BookOpen :size="40" />
          </div>
          <div class="max-w-xs">
            <h2 class="text-xl font-bold text-slate-900">Selecione uma turma</h2>
            <p class="text-slate-500 mt-2">Escolha o vínculo para gerenciar seus planos.</p>
          </div>
        </div>
      </div>

      <!-- Modal Nova Avaliação -->
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
         <div class="bg-white w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl animate-in zoom-in-95 duration-300 border border-slate-100">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
               <div>
                  <h3 class="text-2xl font-bold text-slate-900">Nova Avaliação</h3>
                  <p class="text-slate-500 text-sm font-medium mt-1">Configure o instrumento pedagógico.</p>
               </div>
               <button @click="showModal = false" class="p-2 hover:bg-slate-200 rounded-xl transition-colors text-slate-400">
                  <Plus class="rotate-45" :size="24" />
               </button>
            </div>
            
            <form @submit.prevent="submit" class="p-8 space-y-6">
               <div class="space-y-4">
                  <div class="space-y-2">
                     <label class="text-sm font-bold text-slate-700 ml-1">Bimestre</label>
                     <div class="relative">
                        <select 
                           v-model="form.term_id"
                           class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-4 pl-12 text-sm font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none appearance-none"
                        >
                           <option value="">Selecione o bimestre...</option>
                           <option v-for="term in terms" :key="term.id" :value="term.id">{{ term.name }}</option>
                        </select>
                        <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
                     </div>
                  </div>

                  <div class="space-y-2">
                     <label class="text-sm font-bold text-slate-700 ml-1">Título</label>
                     <div class="relative">
                        <input 
                           v-model="form.name"
                           type="text" 
                           placeholder="Ex: Prova Mensal, Trabalho Interdisciplinar..."
                           class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-4 pl-12 text-sm font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                        >
                        <BookOpen class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
                     </div>
                  </div>

                  <div class="grid grid-cols-2 gap-6">
                     <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Valor Máximo</label>
                        <div class="relative">
                           <input 
                              v-model="form.max_score"
                              type="number" 
                              step="0.01"
                              class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-4 pl-12 text-sm font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                           >
                           <Target class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
                        </div>
                     </div>
                     <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Data</label>
                        <div class="relative">
                           <input 
                              v-model="form.date"
                              type="date"
                              class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-4 pl-12 text-sm font-semibold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                           >
                           <CalendarDays class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
                        </div>
                     </div>
                  </div>
               </div>

               <button 
                  type="submit"
                  :disabled="form.processing || !form.name || !form.term_id"
                  class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] disabled:opacity-50 text-white font-bold py-4 rounded-2xl transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3 mt-4"
               >
                  <Plus :size="22" />
                  Confirmar Planejamento
               </button>
            </form>
         </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
