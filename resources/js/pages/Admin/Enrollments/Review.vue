<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { 
  ShieldCheck, 
  Search, 
  GraduationCap, 
  CheckCircle2, 
  XCircle, 
  MoreHorizontal,
  ArrowRight,
  TrendingUp,
  Target
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
  enrollments: any[];
}>();

const searchTerm = ref('');
const filteredEnrollments = computed(() => {
  return props.enrollments.filter(e => 
    e.student_name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
    e.class_name.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

const updateStatus = (id: number, status: string, score: number, attendance: number) => {
  router.patch(route('enrollments.status.update', id), {
    status: status,
    final_score: score,
    attendance_percentage: attendance
  }, {
    preserveState: true,
  });
};

const getStatusColor = (status: string) => {
  if (status === 'approved') return 'text-emerald-600 bg-emerald-50 border-emerald-100';
  if (status === 'retained') return 'text-rose-600 bg-rose-50 border-rose-100';
  return 'text-slate-400 bg-slate-50 border-slate-100';
};
</script>

<template>
  <Head title="Revisão de Notas & Conselho" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-10 py-6 animate-in fade-in duration-700">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
          <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
             <div class="p-2 bg-slate-900 rounded-xl shadow-lg border border-slate-800">
                <ShieldCheck class="text-white" :size="24" />
             </div>
             Painel do Conselho de Classe
          </h2>
          <p class="text-slate-400 text-sm font-medium">Avalie os resultados finais e determine a progressão dos alunos para o próximo ano letivo.</p>
        </div>

        <!-- Search -->
        <div class="relative group min-w-[300px]">
           <input 
             v-model="searchTerm"
             type="text" 
             placeholder="Pesquisar por aluno ou turma..."
             class="w-full bg-white border-slate-200 rounded-2xl px-5 py-3 pl-11 text-xs font-bold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
           >
           <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" :size="16" />
        </div>
      </div>

      <!-- Table Card -->
      <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden ring-1 ring-slate-200/20">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50">
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aluno & Turma Atual</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Média Final (Anual)</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Assiduidade</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Sugestão de Sistema</th>
                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Decisão do Conselho</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="e in filteredEnrollments" :key="e.id" class="hover:bg-slate-50/20 transition-colors group">
                <td class="px-8 py-6">
                   <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 group-hover:text-indigo-400 transition-colors">
                        <GraduationCap :size="18" />
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-900 leading-none">{{ e.student_name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ e.class_name }}</p>
                      </div>
                   </div>
                </td>
                <td class="px-8 py-6 text-center">
                   <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-50 border border-slate-100 rounded-lg">
                      <TrendingUp :size="14" :class="e.final_score >= 5 ? 'text-emerald-500' : 'text-rose-500'" />
                      <span :class="['text-xs font-black', e.final_score >= 5 ? 'text-emerald-600' : 'text-rose-600']">{{ e.final_score.toFixed(1) }}</span>
                   </div>
                </td>
                <td class="px-8 py-6 text-center">
                   <div class="inline-flex flex-col items-center">
                      <span class="text-xs font-bold text-slate-900">{{ e.attendance_percentage }}%</span>
                      <div class="w-16 h-1.5 bg-slate-100 rounded-full mt-1 overflow-hidden">
                         <div class="h-full bg-indigo-500 rounded-full" :style="`width: ${e.attendance_percentage}%`"></div>
                      </div>
                   </div>
                </td>
                <td class="px-8 py-6 text-center">
                   <span :class="['px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border', getStatusColor(e.suggested_status)]">
                      {{ e.suggested_status === 'approved' ? 'Promover' : 'Reter' }}
                   </span>
                </td>
                <td class="px-8 py-6">
                   <div class="flex items-center justify-end gap-2">
                      <button 
                        @click="updateStatus(e.id, 'approved', e.final_score, e.attendance_percentage)"
                        :class="[
                          'p-2 rounded-xl border transition-all border-slate-100 group-hover:block',
                          e.current_status === 'approved' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-400 hover:text-emerald-600 hover:border-emerald-200'
                        ]"
                        title="Promover para Proximo Ano"
                      >
                         <CheckCircle2 :size="18" />
                      </button>
                      <button 
                        @click="updateStatus(e.id, 'retained', e.final_score, e.attendance_percentage)"
                        :class="[
                          'p-2 rounded-xl border transition-all border-slate-100 group-hover:block',
                          e.current_status === 'retained' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-400 hover:text-rose-600 hover:border-rose-200'
                        ]"
                        title="Reter no Mesmo Ano"
                      >
                         <XCircle :size="18" />
                      </button>
                   </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="filteredEnrollments.length === 0" class="p-20 text-center space-y-4">
           <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto">
              <Target :size="32" />
           </div>
           <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic">Nenhuma matrícula ativa encontrada.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
