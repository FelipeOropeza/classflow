<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Users, 
  GraduationCap, 
  Calendar,
  Layers,
  ArrowUpRight,
  Bell,
  CheckCircle2,
  AlertCircle,
  Clock,
  BookOpen,
  ChevronRight,
  TrendingUp,
  History,
  PenTool,
  CalendarDays,
  Target,
  Baby,
  MoreHorizontal
} from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
  role: string;
  userName: string;
  activeYear: string;
  stats?: any;
  recentActivity?: any[];
  myLinks?: any[];
  pendingAttendance?: string[];
  myChildren?: any[];
  upcomingAssessments?: any[];
  schoolEvents?: any[];
}>();

const greeting = computed(() => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Bom dia';
  if (hour < 18) return 'Boa tarde';
  return 'Boa noite';
});

const isAdmin = computed(() => props.role === 'admin' || props.role === 'director');
const isTeacher = computed(() => props.role === 'teacher');
const isGuardian = computed(() => props.role === 'guardian');
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #default>
      <div class="max-w-6xl mx-auto space-y-10 py-4 animate-in fade-in duration-1000">
        
        <!-- Minimalist Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-100 pb-8 gap-6">
          <div class="space-y-1">
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">{{ greeting }}, <span class="text-indigo-600">{{ userName }}</span></h2>
            <p class="text-slate-400 text-sm">Resumo operacional da unidade escolar • {{ activeYear }}</p>
          </div>
          
          <div class="flex items-center gap-2">
             <button class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-all border border-transparent hover:border-slate-100">
                <Bell :size="18" />
             </button>
             <div class="h-6 w-px bg-slate-100 mx-1"></div>
             <div class="px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-xs font-semibold text-slate-600">
                Calendário Letivo Ativo
             </div>
          </div>
        </div>

        <!-- Role Based Views -->

        <!-- 1. ADMIN / DIRECTOR -->
        <div v-if="isAdmin" class="space-y-10">
           <!-- Refined Stats Grid -->
           <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
             <div v-for="(val, label) in {
               'Alunos': {v: stats.totalStudents, icon: GraduationCap, color: 'indigo'},
               'Turmas': {v: stats.activeClasses, icon: Layers, color: 'slate'},
               'Docentes': {v: stats.totalTeachers, icon: Users, color: 'indigo'},
               'Matrículas': {v: stats.newEnrollments, icon: ArrowUpRight, color: 'emerald'}
             }" :key="label" 
             class="bg-white border border-slate-100 p-5 rounded-xl hover:border-slate-200 transition-colors"
             >
                <div class="flex items-center justify-between mb-3">
                   <div :class="`text-slate-400`">
                      <component :is="val.icon" :size="16" />
                   </div>
                   <span v-if="val.color === 'emerald'" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded uppercase tracking-tighter">Mês Atual</span>
                </div>
                <div class="space-y-0.5">
                   <p class="text-2xl font-bold text-slate-900 leading-tight">{{ val.v }}</p>
                   <p class="text-[11px] font-medium text-slate-400">{{ label }} na instituição</p>
                </div>
             </div>
           </div>

           <!-- Content Grid -->
           <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
              <!-- Activity Feed (Clean) -->
              <div class="lg:col-span-7 space-y-6">
                 <div class="flex items-center justify-between px-1">
                    <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-widest flex items-center gap-2">
                       <History :size="14" class="text-slate-400" />
                       Atividade Recente
                    </h3>
                    <Link href="#" class="text-xs font-bold text-indigo-600 hover:underline">Ver Jornada</Link>
                 </div>
                 
                 <div class="bg-white border border-slate-100 rounded-xl overflow-hidden shadow-sm shadow-slate-100/50">
                    <div v-if="recentActivity?.length === 0" class="p-12 text-center text-slate-300 text-xs italic">Nenhuma movimentação registrada.</div>
                    <div v-for="(item, i) in recentActivity" :key="i" 
                      class="flex items-center justify-between p-5 border-b border-slate-50 last:border-0 hover:bg-slate-50/30 transition-colors"
                    >
                       <div class="flex items-center gap-4">
                          <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center font-bold text-slate-400 uppercase text-[10px]">{{ item.student[0] }}</div>
                          <div class="min-w-0">
                             <p class="text-sm font-medium text-slate-800 truncate">{{ item.student }}</p>
                             <p class="text-[11px] text-slate-400">{{ item.detail }}</p>
                          </div>
                       </div>
                       <div class="text-right shrink-0 ml-4">
                          <span class="text-[10px] font-bold text-slate-300 block mb-1 uppercase">{{ item.date }}</span>
                          <div class="h-1 w-8 bg-indigo-100 rounded-full ml-auto"></div>
                       </div>
                    </div>
                 </div>
              </div>

              <!-- Agenda (Minimalist) -->
              <div class="lg:col-span-5 space-y-6">
                 <div class="flex items-center justify-between px-1">
                    <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-widest flex items-center gap-2">
                       <CalendarDays :size="14" class="text-slate-400" />
                       Agenda Institucional
                    </h3>
                    <button class="text-slate-400 hover:text-slate-600 transition-colors">
                       <MoreHorizontal :size="16" />
                    </button>
                 </div>

                 <div class="space-y-4">
                    <div v-for="event in schoolEvents" :key="event.id" class="bg-white border border-slate-100 p-4 rounded-xl flex items-start gap-4">
                       <div class="w-10 h-10 border border-slate-100 rounded-lg flex flex-col items-center justify-center shrink-0">
                          <span class="text-[9px] font-black uppercase text-slate-400 leading-none">{{ event.month }}</span>
                          <span class="text-base font-bold text-slate-800 leading-none mt-0.5">{{ event.day }}</span>
                       </div>
                       <div class="min-w-0 space-y-1">
                          <p class="text-sm font-bold text-slate-900 truncate tracking-tight">{{ event.title }}</p>
                          <p class="text-[11px] text-slate-400 font-medium leading-relaxed">{{ event.description || 'Evento pedagógico agendado.' }}</p>
                       </div>
                    </div>
                    <div v-if="schoolEvents?.length === 0" class="py-12 border border-dashed border-slate-200 rounded-xl text-center">
                        <p class="text-xs text-slate-400 font-medium italic">Nenhum evento para os próximos dias.</p>
                    </div>
                    <Link :href="route('school-events.index')" class="w-full block py-3 text-center border border-slate-100 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition-all">Explorar Calendário Completo</Link>
                 </div>
              </div>
           </div>
        </div>

        <!-- 2. TEACHER View -->
        <div v-if="isTeacher" class="space-y-10">
           <!-- Quick Insights -->
           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             <div v-for="(val, label) in {
               'Minhas Turmas': {v: stats.myClasses, icon: Layers},
               'Alunos Atendidos': {v: stats.myStudents, icon: GraduationCap},
               'Avaliações': {v: stats.assessmentsPlanned, icon: PenTool}
             }" :key="label" class="bg-white border border-slate-100 p-6 rounded-xl space-y-4">
                <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400">
                   <component :is="val.icon" :size="18" />
                </div>
                <div>
                   <p class="text-3xl font-bold text-slate-900 tracking-tight">{{ val.v }}</p>
                   <p class="text-xs font-medium text-slate-400 uppercase tracking-widest mt-1">{{ label }}</p>
                </div>
             </div>
           </div>

           <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
              <!-- Pending Tasks (Daily Call) -->
              <div class="lg:col-span-3 space-y-6">
                 <div class="bg-indigo-50/30 border border-indigo-100 p-8 rounded-2xl flex items-start gap-6 relative overflow-hidden group">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white shrink-0">
                       <Clock :size="24" />
                    </div>
                    <div class="space-y-5 relative z-10">
                       <div>
                          <h3 class="text-lg font-bold text-indigo-900 leading-tight">Pendências de Frequência</h3>
                          <p class="text-indigo-600 text-sm font-medium mt-1">Algumas turmas ainda não tiveram a chamada concluída hoje.</p>
                       </div>
                       
                       <div v-if="(pendingAttendance?.length ?? 0) > 0" class="flex flex-wrap gap-2">
                          <div v-for="item in pendingAttendance" :key="item" class="px-3 py-1.5 bg-white border border-indigo-100 text-indigo-600 text-[11px] font-bold rounded-lg shadow-sm">
                             {{ item }}
                          </div>
                       </div>
                       <div v-else class="text-emerald-600 font-bold text-sm flex items-center gap-2">
                          <CheckCircle2 :size="18" /> Parabéns! Todas as frequências enviadas.
                       </div>

                       <Link :href="route('attendance.index')" class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all active:scale-95">
                          Resolver Pendências <ChevronRight :size="14" />
                       </Link>
                    </div>
                 </div>
              </div>

              <!-- Links Quick List -->
              <div class="lg:col-span-2 space-y-6 px-1">
                 <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <BookOpen :size="14" />
                    Acesso Rápido
                 </h3>
                 <div class="space-y-2">
                    <Link v-for="link in myLinks?.slice(0, 4)" :key="link.id" :href="route('attendance.index', {link_id: link.id})" 
                      class="flex items-center justify-between p-4 bg-white border border-slate-100 hover:border-slate-200 rounded-xl transition-all group"
                    >
                       <div class="flex items-center gap-3">
                          <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                          <span class="text-sm font-bold text-slate-700">{{ link.school_class.name }} <span class="text-slate-300 mx-1">•</span> {{ link.subject.name }}</span>
                       </div>
                       <ChevronRight :size="16" class="text-slate-300 group-hover:text-indigo-600 transition-colors" />
                    </Link>
                 </div>
              </div>
           </div>
        </div>

        <!-- 3. Guardian View -->
        <div v-if="isGuardian" class="space-y-10">
           <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <div v-for="child in myChildren" :key="child.id" class="bg-white border border-slate-100 p-8 rounded-2xl space-y-6 hover:shadow-xl hover:shadow-slate-200/20 transition-all">
                <div class="flex items-start justify-between">
                   <div class="flex items-center gap-5">
                      <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                         <Users :size="24" />
                      </div>
                      <div>
                         <h4 class="text-xl font-bold text-slate-900 leading-none">{{ child.name }}</h4>
                         <p class="text-xs font-bold text-indigo-600 mt-2">{{ child.class }}</p>
                      </div>
                   </div>
                   <div class="text-right">
                      <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Frequência</p>
                      <p class="text-lg font-black text-slate-900 leading-none mt-1">{{ child.attendanceRate }}%</p>
                   </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3 pt-2">
                   <Link :href="route('guardian.report-card', { student_id: child.id })" class="py-3 px-4 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase text-center hover:bg-slate-800 transition-all">Boletim Digital</Link>
                   <Link :href="route('guardian.attendance', { student_id: child.id })" class="py-3 px-4 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase text-center hover:bg-slate-50 transition-all">Relatório Faltas</Link>
                </div>
             </div>
           </div>

           <!-- Assessments Agenda (Clean Table Layout) -->
           <div class="space-y-6">
              <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-widest px-1">Atividades & Datas Importantes</h3>
              <div class="bg-white border border-slate-100 rounded-xl overflow-hidden">
                  <div v-if="(upcomingAssessments?.length ?? 0) === 0" class="p-16 text-center text-slate-400 text-sm italic border-dashed border-2 border-slate-50 m-4 rounded-lg">
                     Nenhuma atividade programada para os próximos dias.
                  </div>
                  <div v-for="exam in upcomingAssessments" :key="exam.id" class="p-5 flex items-center justify-between border-b border-slate-50 last:border-0 hover:bg-slate-50/40 transition-colors">
                     <div class="flex items-center gap-6">
                        <div class="text-center min-w-8">
                           <span class="block text-[10px] font-black text-slate-300 uppercase leading-none">{{ new Date(exam.date).toLocaleDateString('pt-BR', {month: 'short'}).replace('.', '') }}</span>
                           <span class="block text-lg font-bold text-slate-800 leading-none mt-0.5">{{ new Date(exam.date).getDate() }}</span>
                        </div>
                        <div>
                           <p class="text-sm font-bold text-slate-800">{{ exam.name }}</p>
                           <p class="text-[11px] font-medium text-indigo-500 uppercase tracking-tighter">{{ exam.class_subject.subject.name }} • {{ exam.class_subject.school_class.name }}</p>
                        </div>
                     </div>
                     <div class="px-3 py-1 bg-slate-50 rounded-lg text-xs font-bold text-slate-400">
                        {{ exam.max_score }} pts
                     </div>
                  </div>
              </div>
           </div>
        </div>

      </div>
    </template>
  </AuthenticatedLayout>
</template>
