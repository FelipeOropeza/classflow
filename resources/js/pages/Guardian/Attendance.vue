<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  ClipboardCheck, 
  ChevronRight, 
  Users, 
  ClipboardList,
  AlertCircle,
  CheckCircle2,
  Calendar,
  History,
  TrendingDown,
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
</script>

<template>
  <Head title="Frequência Escolar" />

  <AuthenticatedLayout>
    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
      
      <!-- Header & Student Switcher -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm ring-1 ring-slate-200/20 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl"></div>
        <div class="space-y-2 relative z-10">
          <Link :href="route('dashboard')" class="flex items-center gap-2 text-indigo-600 font-bold text-xs uppercase tracking-widest hover:gap-3 transition-all mb-4">
             <ArrowLeft :size="14" /> Voltar ao Painel
          </Link>
          <div class="flex items-center gap-4">
             <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <ClipboardCheck :size="28" />
             </div>
             <h2 class="text-3xl font-black text-slate-900 tracking-tight">Frequência Escolar</h2>
          </div>
        </div>

        <div v-if="students.length > 1" class="flex gap-2 p-1.5 bg-slate-100 rounded-2xl relative z-10">
          <button 
            v-for="s in students" 
            :key="s.id"
            @click="selectStudent(s.id)"
            :class="[
              'px-6 py-3 rounded-xl text-sm font-black transition-all active:scale-95',
              selectedStudent.id === s.id ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'
            ]"
          >
            {{ s.name.split(' ')[0] }}
          </button>
        </div>
      </div>

      <!-- Main Statistics Header -->
      <div class="bg-indigo-600 rounded-[40px] p-10 text-white shadow-2xl shadow-indigo-100 relative overflow-hidden group">
         <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-1000"></div>
         <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 relative z-10">
            <div class="space-y-1">
               <span class="text-xs font-black uppercase tracking-widest opacity-60">Status Acadêmico de</span>
               <h3 class="text-4xl font-black">{{ selectedStudent.name }}</h3>
               <p class="text-indigo-100 font-medium italic opacity-80">{{ selectedStudent.enrollments[0]?.school_class.name || 'Matrícula Ativa' }}</p>
            </div>
            <div class="flex gap-4">
                <div class="bg-white/10 backdrop-blur-md px-8 py-5 rounded-[30px] border border-white/20 text-center flex flex-col justify-center">
                   <span class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-1">Média Presença</span>
                   <span class="text-3xl font-black">95%</span>
                </div>
                <div class="bg-indigo-500 px-8 py-5 rounded-[30px] shadow-lg text-center flex flex-col justify-center">
                   <span class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">Total de Faltas</span>
                   <span class="text-3xl font-black">
                      {{ attendanceReport.reduce((acc, curr) => acc + curr.total_absences, 0) }}
                   </span>
                </div>
            </div>
         </div>
      </div>

      <!-- Attendance Table (Ultra Modern) -->
      <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 ring-1 ring-slate-200/20 overflow-hidden">
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
           <h3 class="text-xl font-bold flex items-center gap-2">
              <History class="text-indigo-600" :size="24" />
              Relatório de Faltas por Bimestre
           </h3>
           <Link :href="route('guardian.report-card', { student_id: selectedStudent.id })" class="text-xs font-black uppercase tracking-widest text-indigo-600 bg-white px-4 py-2.5 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-all">Ver Notas</Link>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left border-b border-slate-100">
                <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Disciplina</th>
                <th v-for="term in terms" :key="term.id" class="px-6 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                   {{ term.name }}
                </th>
                <th class="px-10 py-6 text-[10px] font-black text-indigo-400 uppercase tracking-widest text-right">Acumulado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="row in attendanceReport" :key="row.subject_name" class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-10 py-6">
                   <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-[10px] text-slate-400 uppercase group-hover:bg-indigo-600 group-hover:text-white transition-all">
                         {{ row.subject_name.substring(0, 2) }}
                      </div>
                      <span class="font-bold text-slate-800 tracking-tight">{{ row.subject_name }}</span>
                   </div>
                </td>
                <td v-for="termData in row.terms" :key="termData.term_id" class="px-6 py-6 text-center">
                   <span :class="[
                     'px-3 py-1.5 rounded-xl text-sm font-black transition-all',
                     termData.absences > 5 ? 'bg-rose-50 text-rose-600' : 
                     termData.absences > 0 ? 'bg-amber-50 text-amber-600' : 
                     'bg-emerald-50 text-emerald-600 opacity-40'
                   ]">
                      {{ termData.absences }}
                   </span>
                </td>
                <td class="px-10 py-6 text-right">
                   <span :class="[
                     'text-lg font-black',
                     row.total_absences > 15 ? 'text-rose-600' : 'text-slate-900'
                   ]">
                      {{ row.total_absences }}
                   </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State Advice -->
        <div v-if="attendanceReport.length === 0" class="p-20 flex flex-col items-center justify-center text-center space-y-4">
           <AlertCircle :size="48" class="text-slate-200" />
           <p class="text-slate-500 font-bold">Nenhum dado de frequência disponível para este aluno.</p>
        </div>
        
        <div class="px-10 py-8 bg-slate-50 border-t border-slate-100">
           <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 mt-1">
                 <AlertCircle :size="20" />
              </div>
              <div class="max-w-3xl">
                 <h5 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-1">Aviso Pedagógico</h5>
                 <p class="text-slate-500 text-xs font-medium leading-relaxed italic">
                    As faltas são registradas diariamente pelos professores. Caso note alguma divergência, favor entrar em contato com a secretaria acadêmica. Lembre-se que o limite máximo de faltas permitido por lei é de 25% da carga horária total da disciplina.
                 </p>
              </div>
           </div>
        </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
