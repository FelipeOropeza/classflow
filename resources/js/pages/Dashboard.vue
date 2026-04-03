<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { GraduationCap } from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  Users, 
  UserPlus, 
  Calendar, 
  TrendingUp,
  ArrowUpRight,
  ClipboardList
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
  statsData: {
    totalStudents: number;
    activeClasses: number;
    attendanceRate: number;
    newEnrollments: number;
  },
  activeYear: string;
}>();

const stats = computed(() => [
  { 
    label: 'Total de Alunos', 
    value: 'Ativos',
    count: props.statsData.totalStudents, 
    change: '+0%', 
    trend: 'up', 
    icon: GraduationCap, 
    color: 'indigo' 
  },
  { 
    label: 'Turmas Ativas', 
    value: props.activeYear,
    count: props.statsData.activeClasses, 
    change: '+0', 
    trend: 'up', 
    icon: Users, 
    color: 'emerald' 
  },
  { 
    label: 'Frequência Geral', 
    value: 'Média Mensal',
    count: props.statsData.attendanceRate, 
    change: '-0%', 
    trend: 'down', 
    icon: ClipboardList, 
    color: 'amber' 
  },
  { 
    label: 'Novas Matrículas', 
    value: 'Este mês',
    count: props.statsData.newEnrollments, 
    change: '+0%', 
    trend: 'up', 
    icon: UserPlus, 
    color: 'rose' 
  }
]);
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Controle Escolar" />

    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Bom dia, Diretor</h2>
        <p class="mt-1.5 text-slate-500 font-medium tracking-wide leading-relaxed">
          Bem-vindo ao ClassFlow. Aqui está o resumo pedagógico de hoje.
        </p>
      </div>
      <div class="flex gap-3">
         <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-xl shadow-indigo-200 transition-all active:scale-95 text-sm flex items-center gap-2">
            <Plus :size="18" />
            Nova Matrícula
         </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div 
        v-for="stat in stats" 
        :key="stat.label"
        class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/30 hover:shadow-2xl hover:shadow-indigo-100 transition-all duration-300 group"
      >
        <div class="flex items-center justify-between mb-4">
          <div 
            :class="[
              'p-3.5 rounded-2xl transition-transform group-hover:scale-110 duration-300 shadow-inner',
              stat.color === 'indigo' ? 'bg-indigo-50 text-indigo-600' :
              stat.color === 'emerald' ? 'bg-emerald-50 text-emerald-600' :
              stat.color === 'amber' ? 'bg-amber-50 text-amber-600' :
              'bg-rose-50 text-rose-600'
            ]"
          >
            <component :is="stat.icon" :size="22" stroke-width="2.5" />
          </div>
          <div 
            :class="[
              'flex items-center px-2 py-1 rounded-full text-xs font-bold leading-none',
              stat.trend === 'up' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'
            ]"
          >
             {{ stat.change }}
             <ArrowUpRight v-if="stat.trend === 'up'" :size="12" class="ml-1" />
          </div>
        </div>
        <div class="space-y-1">
          <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ stat.label }}</p>
          <div class="flex items-baseline gap-2">
            <span class="text-3xl font-black text-slate-900 tracking-tighter tabular-nums">{{ stat.count }}</span>
            <span class="text-xs font-semibold text-slate-400 italic">/ {{ stat.value }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Recent Activity -->
      <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/30 overflow-hidden">
        <div class="p-8 pb-4 flex items-center justify-between">
           <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
              <TrendingUp :size="20" class="text-indigo-600" />
              Atividades Recentes
           </h3>
           <button class="text-indigo-600 hover:text-indigo-800 text-sm font-bold transition-all px-3 py-1 rounded-lg hover:bg-indigo-50">
              Ver tudo
           </button>
        </div>
        <div class="overflow-x-auto">
           <table class="w-full text-left">
              <thead class="bg-slate-50/50 border-y border-slate-100">
                <tr>
                  <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Aluno / Docente</th>
                  <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Ação</th>
                  <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Status</th>
                  <th class="px-8 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Data</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 italic-none">
                <tr v-for="i in 5" :key="i" class="hover:bg-slate-50/50 transition-colors group">
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 text-xs border border-white shadow-sm ring-2 ring-slate-100 ring-offset-2">
                            JO
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors tracking-tight">João Oliveira</p>
                            <p class="text-xs font-medium text-slate-400 tracking-wide">Matrícula #2026-004</p>
                        </div>
                    </div>
                  </td>
                  <td class="px-6 py-5">
                    <p class="text-sm font-semibold text-slate-600">Lançamento de Notas</p>
                  </td>
                  <td class="px-6 py-5">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700 shadow-sm">
                      Concluído
                    </span>
                  </td>
                  <td class="px-8 py-5">
                    <p class="text-sm font-bold text-slate-400 font-mono tracking-tighter">03/04 - 10:42</p>
                  </td>
                </tr>
              </tbody>
           </table>
        </div>
      </div>

      <!-- Quick Calendar/Info -->
      <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden group">
         <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 rounded-full blur-3xl transition-transform group-hover:scale-150 duration-700"></div>
         <h3 class="text-2xl font-black mb-6 tracking-tight flex items-center gap-2">
            <Calendar :size="24" />
            Agenda Escolar
         </h3>
         <div class="space-y-6 relative z-10">
            <div class="flex items-start gap-4">
                <div class="bg-white/20 p-2.5 rounded-xl font-bold flex flex-col items-center justify-center min-w-[50px] shadow-lg backdrop-blur-sm">
                    <span class="text-xs uppercase tracking-widest opacity-80 leading-none">Abr</span>
                    <span class="text-lg leading-tight tracking-tighter">15</span>
                </div>
                <div class="space-y-1">
                    <p class="text-base font-black tracking-tight leading-none pt-1">Conselho de Classe</p>
                    <p class="text-[13px] font-medium text-indigo-100 italic leading-relaxed">Turno Matutino - 09:00h</p>
                </div>
            </div>
            <div class="flex items-start gap-4 opacity-70 hover:opacity-100 transition-opacity cursor-pointer">
                <div class="bg-white/20 p-2.5 rounded-xl font-bold flex flex-col items-center justify-center min-w-[50px] shadow-lg backdrop-blur-sm">
                    <span class="text-xs uppercase tracking-widest opacity-80 leading-none">Abr</span>
                    <span class="text-lg leading-tight tracking-tighter">22</span>
                </div>
                <div class="space-y-1">
                    <p class="text-base font-black tracking-tight leading-none pt-1">Feriado Nacional</p>
                    <p class="text-[13px] font-medium text-indigo-100 italic leading-relaxed">Tiradentes (Recesso)</p>
                </div>
            </div>
         </div>
         
         <div class="mt-12 p-6 bg-white/10 rounded-3xl backdrop-blur-md border border-white/10 shadow-inner">
             <p class="text-xs font-black uppercase tracking-[0.2em] mb-3 opacity-60">Status do Sistema</p>
             <div class="flex items-center gap-3">
                 <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse shadow-lg shadow-emerald-400/50"></div>
                 <p class="text-sm font-bold tracking-tight">Banco de Dados Sincronizado</p>
             </div>
         </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script lang="ts">
import { Plus } from 'lucide-vue-next';
export default {
    components: { Plus }
}
</script>
