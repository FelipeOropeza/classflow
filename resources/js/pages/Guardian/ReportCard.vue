<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { 
  Baby, 
  ChevronRight, 
  FileText, 
  TrendingUp, 
  GraduationCap, 
  CalendarDays,
  Target,
  BarChart3,
  AlertCircle
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Student {
  id: number;
  name: string;
}

interface TermResult {
  term_name: string;
  average: number | string;
}

interface SubjectReport {
  name: string;
  terms: TermResult[];
  final_average: number | string;
  total_absences: number;
}

const props = defineProps<{
  students: Student[];
  report: SubjectReport[];
  selectedStudent: Student | null;
  terms: { id: number; name: string }[];
}>();

const selectStudent = (id: number) => {
  router.get(route('guardian.report-card'), { student_id: id });
};
</script>

<template>
  <Head title="Boletim Escolar" />

  <AuthenticatedLayout>
    <template #default>
      <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header & Stats Overview -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
              <div class="p-2.5 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                <FileText class="text-white" :size="24" />
              </div>
              Boletim Acadêmico
            </h2>
            <p class="text-slate-500 mt-2 font-medium">Acompanhe o desempenho anual e a pontuação por bimestre.</p>
          </div>

          <!-- Student Select -->
          <div class="flex gap-2">
            <button 
              v-for="student in students" 
              :key="student.id"
              @click="selectStudent(student.id)"
              :class="[
                'px-5 py-3 rounded-2xl font-bold transition-all shadow-sm flex items-center gap-2 border ring-1',
                selectedStudent?.id === student.id 
                  ? 'bg-indigo-600 border-indigo-600 text-white ring-indigo-200' 
                  : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 ring-slate-200/20'
              ]"
            >
              <Baby :size="18" />
              {{ student.name }}
            </button>
          </div>
        </div>

        <!-- Grade Grid Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 ring-1 ring-slate-200/20 overflow-hidden animate-in slide-in-from-bottom duration-500">
          <div class="p-8 border-b border-slate-50 bg-slate-50/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
             <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                   <TrendingUp :size="24" />
                </div>
                <div>
                   <h3 class="text-xl font-bold text-slate-900">Mapa de Pontuação - {{ selectedStudent?.name }}</h3>
                   <p class="text-slate-500 text-sm font-medium">Relatório detalhado anual (Ano Letivo 2026)</p>
                </div>
             </div>
             
             <div class="flex items-center gap-2 px-4 py-2 bg-indigo-50/50 rounded-xl text-xs font-black text-indigo-600 uppercase tracking-widest border border-indigo-100/50">
                <AlertCircle :size="14" />
                Nota de Corte: 7.0
             </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50">
                  <th class="px-8 py-5 text-xs font-black text-slate-400 border-b border-slate-100 uppercase tracking-widest">Disciplina</th>
                  
                  <th v-for="term in terms" :key="term.id" class="px-8 py-5 text-xs font-black text-slate-400 border-b border-slate-100 uppercase tracking-widest text-center">
                    {{ term.name }}
                  </th>

                  <th class="px-8 py-5 text-xs font-black text-indigo-600 border-b border-indigo-100 uppercase tracking-widest text-center bg-indigo-50/30">
                    Média Final
                  </th>

                  <th class="px-8 py-5 text-xs font-black text-rose-500 border-b border-rose-100 uppercase tracking-widest text-center">
                    Faltas
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="item in report" :key="item.name" class="hover:bg-slate-50/50 transition-colors group">
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-3">
                       <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all font-bold">
                          {{ item.name.charAt(0) }}
                       </div>
                       <span class="font-bold text-slate-700">{{ item.name }}</span>
                    </div>
                  </td>
                  
                  <td v-for="(termValue, tIdx) in item.terms" :key="tIdx" class="px-8 py-6 text-center">
                    <span 
                      :class="[
                        'px-4 py-2 rounded-xl font-bold text-sm border ring-1',
                        (typeof termValue.average === 'number' && termValue.average < 7) 
                          ? 'bg-rose-50 border-rose-100 text-rose-600 ring-rose-200/10' 
                          : termValue.average === '-' 
                            ? 'bg-slate-50 border-slate-100 text-slate-400 ring-transparent'
                            : 'bg-emerald-50 border-emerald-100 text-emerald-700 ring-emerald-200/10'
                      ]"
                    >
                      {{ termValue.average }}
                    </span>
                  </td>

                  <td class="px-8 py-6 text-center bg-indigo-50/20 font-black text-indigo-600">
                    <div 
                      :class="[
                        'inline-flex items-center justify-center px-5 py-2.5 rounded-2xl text-lg shadow-sm border ring-1',
                        (typeof item.final_average === 'number' && item.final_average < 7) 
                          ? 'bg-rose-100 border-rose-200 text-rose-700 ring-rose-100' 
                          : item.final_average === '-' 
                            ? 'bg-slate-50 border-slate-100 text-slate-400 ring-transparent'
                            : 'bg-indigo-600 border-indigo-600 text-white ring-indigo-200 shadow-indigo-100'
                      ]"
                    >
                       {{ item.final_average }}
                    </div>
                  </td>

                  <td class="px-8 py-6 text-center font-bold text-slate-500">
                    <span :class="['px-3 py-1.5 rounded-lg border', item.total_absences > 10 ? 'bg-rose-50 border-rose-100 text-rose-600' : 'bg-slate-50 border-slate-100']">
                      {{ item.total_absences }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="report.length === 0" class="p-20 text-center flex flex-col items-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 ring-1 ring-slate-100">
              <BarChart3 :size="32" />
            </div>
            <h4 class="text-slate-900 font-bold">Nenhum dado pedagógico disponível</h4>
            <p class="text-slate-500 text-sm mt-1 max-w-sm">Aguarde o lançamento de notas e frequências pela equipe técnica para visualizar o progresso do aluno.</p>
          </div>
        </div>

        <!-- Explanatory Footnote -->
        <div class="flex items-start gap-4 p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-500 text-sm font-medium leading-relaxed">
           <AlertCircle class="text-indigo-400 shrink-0 mt-0.5" :size="20" />
           <p>
              As médias bimestrais são calculadas combinando todas as atividades avaliativas do período. A <b>Média Final</b> é calculada proporcionalmente ao ciclo dos quatro bimestres. Alunos com média inferior a 7.0 em qualquer disciplina devem buscar o cronograma de reforço escolar.
           </p>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
