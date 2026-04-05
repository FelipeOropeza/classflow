<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  ClipboardCheck, 
  ChevronRight, 
  Users, 
  AlertCircle,
  History,
  ArrowLeft
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  students: any[];
  attendanceReport: any[];
  selectedStudent: any;
  terms: any[];
}>();

const selectStudent = (id: number) => {
  router.get(route('guardian.attendance'), { student_id: id }, { preserveState: true });
};

const getAbsenceStyle = (count: number) => {
  if (count > 5) return 'text-rose-600 font-black';
  if (count > 0) return 'text-amber-500 font-bold';
  return 'text-slate-300 font-medium';
};
</script>

<template>
  <Head title="Frequência Escolar" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-10 py-4 animate-in fade-in duration-700">
      
      <!-- Minimalist Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-100 pb-8 gap-6">
        <div class="space-y-1">
          <Link :href="route('dashboard')" class="flex items-center gap-1.5 text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:text-indigo-600 transition-all mb-4 px-1 group">
             <ArrowLeft :size="12" class="group-hover:-translate-x-0.5 transition-transform" /> Voltar ao Painel
          </Link>
          <div class="flex items-center gap-4">
             <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Frequência Escolar</h2>
             <span class="px-2 py-0.5 bg-slate-50 border border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-widest rounded-lg">Faltas por Bimestre</span>
          </div>
        </div>

        <!-- Student Switcher (Minimal) -->
        <div v-if="students.length > 1" class="flex gap-1 p-1 bg-slate-50 border border-slate-100 rounded-xl">
          <button 
            v-for="s in students" 
            :key="s.id"
            @click="selectStudent(s.id)"
            :class="[
              'px-5 py-2.5 rounded-lg text-xs font-bold transition-all active:scale-95',
              selectedStudent.id === s.id ? 'bg-white text-indigo-600 shadow-sm border border-slate-100' : 'text-slate-400 hover:text-slate-600'
            ]"
          >
            {{ s.name.split(' ')[0] }}
          </button>
        </div>
      </div>

      <!-- Quick Summary Row -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
         <div class="bg-white border border-slate-100 p-6 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
               <Users :size="18" />
            </div>
            <div>
               <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1.5">Matrícula</p>
               <p class="text-sm font-bold text-slate-800 truncate">{{ selectedStudent.enrollments[0]?.school_class.name || 'Ativa' }}</p>
            </div>
         </div>
         
         <div class="bg-white border border-slate-100 p-6 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-indigo-500">
               <ClipboardCheck :size="18" />
            </div>
            <div>
               <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1.5">Frequência</p>
               <p class="text-sm font-bold text-slate-800">95% de Presença</p>
            </div>
         </div>

         <div class="bg-slate-900 p-6 rounded-xl flex items-center gap-4 text-white shadow-xl shadow-slate-900/10">
            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-white/60">
               <History :size="18" />
            </div>
            <div>
               <p class="text-[10px] font-black uppercase tracking-widest leading-none mb-1.5 opacity-60">Total de Faltas</p>
               <p class="text-sm font-bold tracking-tight">
                  {{ attendanceReport.reduce((acc, curr) => acc + curr.total_absences, 0) }} registros este ano
               </p>
            </div>
         </div>
      </div>

      <!-- Attendance Table (High Fidelity Data Layout) -->
      <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm shadow-slate-100/30">
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-100">
              <tr>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white">Matéria</th>
                <th v-for="term in terms" :key="term.id" class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center border-l border-slate-100/50">
                   {{ term.name }}
                </th>
                <th class="px-8 py-5 text-[10px] font-black text-indigo-500 uppercase tracking-widest text-right bg-slate-50/20 border-l border-slate-100/50">Acumulado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="row in attendanceReport" :key="row.subject_name" class="hover:bg-slate-50/30 transition-colors group">
                <td class="px-8 py-6">
                   <div class="flex items-center gap-3">
                      <div class="w-7 h-7 rounded bg-slate-50 flex items-center justify-center font-bold text-[9px] text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                         {{ row.subject_name.substring(0, 2) }}
                      </div>
                      <span class="text-sm font-semibold text-slate-700 tracking-tight">{{ row.subject_name }}</span>
                   </div>
                </td>
                <td v-for="termData in row.terms" :key="termData.term_id" class="px-6 py-6 text-center border-l border-slate-50/50">
                   <span :class="[ 'text-sm transition-all', getAbsenceStyle(termData.absences) ]">
                      {{ termData.absences === 0 ? '—' : termData.absences }}
                   </span>
                </td>
                <td class="px-8 py-6 text-right bg-slate-50/10 border-l border-slate-50/50">
                   <span :class="[
                     'text-sm font-black',
                     row.total_absences >= 10 ? 'text-rose-500' : 'text-slate-900'
                   ]">
                      {{ row.total_absences }}
                   </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="attendanceReport.length === 0" class="p-24 flex flex-col items-center justify-center text-center space-y-3">
           <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
              <AlertCircle :size="32" />
           </div>
           <p class="text-slate-400 text-sm font-medium italic">Nenhum registro de frequência localizado.</p>
        </div>
        
        <!-- Footer Advice (Subtle) -->
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
           <div class="flex items-start gap-4">
              <AlertCircle :size="16" class="text-slate-300 mt-0.5" />
              <p class="text-[11px] text-slate-400 font-medium leading-relaxed max-w-4xl italic">
                 O controle de frequência é atualizado em tempo real pelos docentes. Em caso de inconsistências, o responsável legal deve solicitar a retificação junto à diretoria acadêmica no prazo máximo de 48 horas após a aula original.
              </p>
           </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
