<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { 
  Baby, 
  ChevronRight, 
  FileText, 
  TrendingUp, 
  GraduationCap, 
  CalendarDays,
  Target,
  BarChart3,
  AlertCircle,
  ArrowLeft,
  ArrowUpRight
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

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
  router.get(route('guardian.report-card'), { student_id: id }, { preserveState: true });
};

const getGradeStyle = (grade: number | string) => {
  if (grade === '-') return 'text-slate-300 font-medium';
  const val = Number(grade);
  if (val < 5) return 'text-rose-500 font-bold';
  return 'text-slate-700 font-bold';
};

const overallAverage = computed(() => {
  const finalGrades = props.report
    .map(item => item.final_average)
    .filter(avg => typeof avg === 'number') as number[];
  
  if (finalGrades.length === 0) return '-';
  const sum = finalGrades.reduce((a, b) => a + b, 0);
  return (sum / finalGrades.length).toFixed(1);
});

const performanceLabel = computed(() => {
  const avg = Number(overallAverage.value);
  if (isNaN(avg)) return 'Aguardando Notas';
  if (avg >= 9) return 'Desempenho de Excelência';
  if (avg >= 7) return 'Aproveitamento Superior';
  if (avg >= 5) return 'Dentro das Metas';
  return 'Necessita de Atenção';
});
</script>

<template>
  <Head title="Boletim Escolar" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-10 py-4 animate-in fade-in duration-700">
      
      <!-- Minimalist Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-100 pb-8 gap-6">
        <div class="space-y-1">
          <Link :href="route('dashboard')" class="flex items-center gap-1.5 text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:text-indigo-600 transition-all mb-4 px-1 group">
             <ArrowLeft :size="12" class="group-hover:-translate-x-0.5 transition-transform" /> Voltar ao Painel
          </Link>
          <div class="flex items-center gap-4">
             <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Boletim Acadêmico</h2>
             <span class="px-2 py-0.5 bg-slate-50 border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-widest rounded-lg">Ano Letivo 2026</span>
          </div>
        </div>

        <div class="flex flex-col md:items-end gap-4">
          <!-- Student Switcher (Minimal) -->
          <div v-if="students.length > 1" class="flex gap-1 p-1 bg-slate-50 border border-slate-100 rounded-xl">
            <button 
              v-for="s in students" 
              :key="s.id"
              @click="selectStudent(s.id)"
              :class="[
                'px-5 py-2.5 rounded-lg text-xs font-bold transition-all active:scale-95',
                selectedStudent?.id === s.id ? 'bg-white text-indigo-600 shadow-sm border border-slate-100' : 'text-slate-400 hover:text-slate-600'
              ]"
            >
              {{ s.name.split(' ')[0] }}
            </button>
          </div>

          <!-- Download PDF -->
          <a :href="route('guardian.report-card.pdf', { student_id: selectedStudent?.id })" target="_blank"
             class="flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 text-white text-[10px] uppercase tracking-widest font-bold rounded-lg hover:bg-indigo-600 transition-colors focus:ring-4 focus:ring-slate-100 outline-none">
             <FileText :size="14" /> Exportar Oficial
          </a>
        </div>
      </div>

      <!-- Performance Insights Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
         <div class="bg-white border border-slate-100 p-6 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
               <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Matrícula Ativa</p>
               <GraduationCap :size="14" class="text-slate-200" />
            </div>
            <p class="text-lg font-bold text-slate-800 tracking-tight leading-none">{{ selectedStudent?.name }}</p>
         </div>

         <div class="bg-white border border-slate-100 p-6 rounded-xl space-y-3">
            <div class="flex items-center justify-between">
               <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Média Geral</p>
               <TrendingUp :size="14" class="text-indigo-400" />
            </div>
            <div class="flex items-end gap-2">
               <p class="text-2xl font-black text-slate-900 leading-none">{{ overallAverage }}</p>
               <span :class="['text-[10px] font-bold mb-0.5 tracking-tighter', Number(overallAverage) >= 5 ? 'text-emerald-500' : 'text-rose-500']">
                 {{ performanceLabel }}
               </span>
            </div>
         </div>

         <div class="bg-slate-900 p-6 rounded-xl space-y-3 text-white shadow-xl shadow-slate-900/10">
            <div class="flex items-center justify-between">
               <p class="text-[10px] font-black uppercase tracking-widest opacity-40">Regras de Aprovação</p>
               <AlertCircle :size="14" class="opacity-40" />
            </div>
            <p class="text-sm font-bold tracking-tight">Média mínima 5.0 para aprovação direta</p>
         </div>
      </div>

      <!-- Report Card Table (Sharp & Professional) -->
      <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm shadow-slate-100/30">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100 font-sans">
              <tr>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">Componente Curricular</th>
                <th v-for="term in terms" :key="term.id" class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center border-l border-slate-100/50">
                   {{ term.name }}
                </th>
                <th class="px-8 py-5 text-[10px] font-black text-indigo-500 uppercase tracking-widest text-right bg-slate-50/20 border-l border-slate-100/50">Média Final</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="item in report" :key="item.name" class="hover:bg-slate-50/30 transition-colors group">
                <td class="px-8 py-6">
                   <div class="flex items-center gap-3">
                      <div class="w-7 h-7 rounded bg-slate-50 flex items-center justify-center font-bold text-[9px] text-slate-300 uppercase group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                         {{ item.name.charAt(0) }}
                      </div>
                      <span class="text-sm font-semibold text-slate-700 tracking-tight">{{ item.name }}</span>
                   </div>
                </td>
                
                <td v-for="(termVal, tIdx) in item.terms" :key="tIdx" class="px-6 py-6 text-center border-l border-slate-50/50">
                   <span :class="[ 'text-sm transition-all', getGradeStyle(termVal.average) ]">
                      {{ termVal.average }}
                   </span>
                </td>

                <td class="px-8 py-6 text-right bg-slate-50/10 border-l border-slate-50/50">
                   <span :class="[
                     'text-sm font-black px-3 py-1 rounded-lg border',
                     (typeof item.final_average === 'number' && item.final_average < 7) 
                        ? 'text-rose-500 border-rose-100 bg-rose-50' 
                        : item.final_average === '-' 
                          ? 'text-slate-300 border-transparent'
                          : 'text-indigo-600 border-indigo-100 bg-indigo-50'
                   ]">
                      {{ item.final_average }}
                   </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="report.length === 0" class="p-24 flex flex-col items-center justify-center text-center space-y-4">
           <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-100 border border-slate-100/50">
              <BarChart3 :size="32" />
           </div>
           <p class="text-slate-400 text-sm font-medium italic">Aguardando fechamento do primeiro período letivo.</p>
        </div>
        
        <!-- Subtle Academic Legalese -->
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
           <div class="flex items-start gap-4">
              <AlertCircle :size="16" class="text-slate-300 mt-0.5" />
              <p class="text-[11px] text-slate-400 font-medium leading-relaxed max-w-4xl italic">
                 Este documento é uma representação digital simplificada do boletim oficial. As médias podem sofrer alterações caso haja reclassificação ou prova de recuperação conforme o regimento interno da instituição. Para certificados de transferência, consulte a secretaria.
              </p>
           </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
