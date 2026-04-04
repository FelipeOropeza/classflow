<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
  BarChart3, 
  Search, 
  GraduationCap, 
  User, 
  Save, 
  ChevronRight, 
  LayoutPanelLeft,
  AlertCircle,
  ClipboardList
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Link {
  id: number;
  school_class: { name: string };
  subject: { name: string };
}

interface Assessment {
  id: number;
  name: string;
  max_score: number;
}

interface StudentGrade {
  id: number;
  student_name: string;
  registration: string;
  grade_id: number | null;
  score: number | null;
}

const props = defineProps<{
  links: Link[];
  assessments: Assessment[];
  students: StudentGrade[];
  filters: { link_id: string | null; assessment_id: string | null };
  selectedLink: Link | null;
  selectedAssessment: Assessment | null;
}>();

const selectedLinkLocal = ref(props.filters.link_id || '');
const selectedAssessmentLocal = ref(props.filters.assessment_id || '');

const form = useForm({
  assessment_id: props.filters.assessment_id || '',
  grades: [] as { enrollment_id: number; score: number | null }[]
});

// Inicializar formulário quando os alunos carregarem
watch(() => props.students, (newStudents) => {
  if (newStudents.length > 0) {
    form.grades = newStudents.map(student => ({
      enrollment_id: student.id,
      score: student.score
    }));
  } else {
    form.grades = [];
  }
}, { immediate: true });

const changeLink = () => {
  selectedAssessmentLocal.value = ''; // Reset assessment ao mudar turma
  router.get(route('grades.index'), { link_id: selectedLinkLocal.value }, {
    preserveState: true,
    replace: true,
  });
};

const changeAssessment = () => {
  router.get(route('grades.index'), { 
    link_id: selectedLinkLocal.value, 
    assessment_id: selectedAssessmentLocal.value 
  }, {
    preserveState: true,
    replace: true,
  });
  form.assessment_id = selectedAssessmentLocal.value;
};

const submit = () => {
  form.post(route('grades.store'), {
    preserveScroll: true,
    onSuccess: () => {
      // Feedback visual se necessário
    }
  });
};
</script>

<template>
  <Head title="Lançar Notas" />

  <AuthenticatedLayout>
    <template #default>
      <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
              <div class="p-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                <BarChart3 class="text-white" :size="24" />
              </div>
              Lançamento de Notas
            </h2>
            <p class="text-slate-500 mt-2 font-medium">Selecione a avaliação planejada para registrar o desempenho dos alunos.</p>
          </div>
        </div>

        <!-- Selection Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <!-- Link Selection -->
          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1">Turma & Disciplina</label>
            <div class="relative">
              <select 
                v-model="selectedLinkLocal"
                @change="changeLink"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer appearance-none"
              >
                <option value="">Selecione uma turma...</option>
                <option v-for="link in links" :key="link.id" :value="link.id">
                  {{ link.school_class.name }} - {{ link.subject.name }}
                </option>
              </select>
              <LayoutPanelLeft class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>

          <!-- Assessment Selection -->
          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1">Avaliação Planejada</label>
            <div class="relative">
              <select 
                v-model="selectedAssessmentLocal"
                @change="changeAssessment"
                :disabled="!selectedLinkLocal"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer appearance-none disabled:opacity-50"
              >
                <option value="">Selecione a avaliação...</option>
                <option v-for="assessment in assessments" :key="assessment.id" :value="assessment.id">
                  {{ assessment.name }} (Valor Máx: {{ assessment.max_score }})
                </option>
              </select>
              <ClipboardList class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>
        </div>

        <!-- Students List -->
        <div v-if="selectedAssessment" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden ring-1 ring-slate-200/20 animate-in slide-in-from-bottom duration-500">
          <div class="p-8 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <GraduationCap :size="24" />
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900">Alunos Matriculados</h3>
                <p class="text-slate-500 text-sm font-medium">Turma: {{ selectedLink?.school_class.name }} | {{ selectedAssessment.name }}</p>
              </div>
            </div>
            
            <button 
               @click="submit"
               :disabled="form.processing"
               class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 disabled:opacity-50 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg shadow-indigo-200 flex items-center gap-2"
            >
               <Save v-if="!form.processing" :size="20" />
               <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
               {{ form.processing ? 'Salvando...' : 'Salvar Alterações' }}
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50">
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider">Aluno</th>
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider">Matrícula</th>
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider w-48 text-center">Nota (Máx: {{ selectedAssessment.max_score }})</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(grade, index) in form.grades" :key="grade.enrollment_id" class="hover:bg-slate-50/50 transition-colors group">
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-400 transition-colors capitalize font-bold">
                        {{ students[index].student_name.charAt(0) }}
                      </div>
                      <span class="font-bold text-slate-700">{{ students[index].student_name }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-6">
                    <span class="text-sm font-mono text-slate-400 group-hover:text-slate-600 transition-colors">{{ students[index].registration }}</span>
                  </td>
                  <td class="px-8 py-6">
                    <div class="flex justify-center">
                      <div class="relative group/input max-w-[120px]">
                        <input 
                          v-model="grade.score"
                          type="number" 
                          step="0.01"
                          :max="selectedAssessment.max_score"
                          min="0"
                          placeholder="--"
                          class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-center font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                        >
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover/input:opacity-100 transition-opacity">
                          <BarChart3 :size="14" class="text-slate-300" />
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="form.grades.length === 0" class="p-16 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
              <User :size="32" />
            </div>
            <h4 class="text-slate-900 font-bold">Nenhum aluno encontrado</h4>
            <p class="text-slate-500 text-sm mt-1">Não existem matrículas ativas para esta turma.</p>
          </div>
        </div>

        <!-- Empty States Selection -->
        <div v-else class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center text-center space-y-6 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-400">
            <Search :size="40" />
          </div>
          <div class="max-w-sm">
            <h2 class="text-xl font-bold text-slate-900">{{ !selectedLinkLocal ? 'Selecione a Turma' : 'Selecione a Avaliação' }}</h2>
            <p class="text-slate-500 mt-2">
              {{ !selectedLinkLocal 
                ? 'Escolha a disciplina e turma para visualizar os planos de aula e avaliações.' 
                : 'Você precisa selecionar qual avaliação (prova, trabalho) deseja lançar as notas dos alunos.' 
              }}
            </p>
            <div v-if="selectedLinkLocal && assessments.length === 0" class="mt-8 p-4 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-3 text-left">
               <AlertCircle class="text-amber-500 shrink-0 mt-0.5" :size="20" />
               <div>
                  <p class="text-amber-800 font-bold text-sm">Nenhuma avaliação planejada</p>
                  <p class="text-amber-700 text-xs mt-1 leading-relaxed">Vá até o menu <b>Planejar Avaliações</b> para criar as provas desta turma antes de prosseguir.</p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
