<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Users, 
  GraduationCap, 
  ClipboardCheck, 
  Calendar,
  Layers,
  ArrowUpRight,
  ArrowDownRight,
  Search,
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
  Baby
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
      <div class="space-y-8 animate-in fade-in duration-700">
        
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
          <div class="space-y-1">
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight">{{ greeting }}, <span class="capitalize">{{ userName }}</span></h2>
            <p class="text-slate-500 font-medium">Bem-vindo ao ClassFlow. Aqui está o resumo pedagógico de hoje.</p>
          </div>
          
          <div class="flex items-center gap-3 bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
             <div class="flex items-center gap-2.5 px-4 py-2 bg-indigo-50/50 rounded-xl">
                <Calendar :size="18" class="text-indigo-600" />
                <span class="text-sm font-bold text-indigo-900">{{ activeYear }}</span>
             </div>
             <button class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                <Bell :size="20" />
             </button>
          </div>
        </div>

        <!-- Role Based Dashboards -->

        <!-- 1. ADMIN / DIRECTOR DASHBOARD -->
        <div v-if="isAdmin" class="space-y-8">
           <!-- Admin Stats -->
           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
             <div v-for="(val, label) in {
               'Total de Alunos': {v: stats.totalStudents, icon: GraduationCap, color: 'indigo'},
               'Turmas Ativas': {v: stats.activeClasses, icon: Layers, color: 'emerald'},
               'Professores': {v: stats.totalTeachers, icon: Users, color: 'amber'},
               'Novas Matrículas': {v: stats.newEnrollments, icon: ArrowUpRight, color: 'rose'}
             }" :key="label" 
             class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 ring-1 ring-slate-200/20 hover:shadow-md transition-all group overflow-hidden relative"
             >
                <div class="flex items-center justify-between mb-4">
                   <div :class="`w-12 h-12 rounded-2xl bg-${val.color}-50 flex items-center justify-center text-${val.color}-600 group-hover:scale-110 transition-transform`">
                      <component :is="val.icon" :size="24" />
                   </div>
                   <div class="flex items-center gap-1 text-emerald-500 text-xs font-black bg-emerald-50 px-2 py-1 rounded-lg">
                      <TrendingUp :size="10" />
                      +0%
                   </div>
                </div>
                <div class="space-y-1">
                   <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ label }}</p>
                   <p class="text-3xl font-black text-slate-900 leading-none">{{ val.v }}</p>
                </div>
             </div>
           </div>

           <!-- Recent Activity & Calendar -->
           <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden ring-1 ring-slate-200/20">
                 <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                       <History class="text-indigo-600" :size="20" />
                       Atividades Recentes
                    </h3>
                    <Link href="#" class="text-sm font-bold text-indigo-600 hover:text-indigo-700">Ver tudo</Link>
                 </div>
                 <div class="divide-y divide-slate-50">
                    <div v-for="item in recentActivity" :key="item.student" class="px-8 py-5 flex items-center justify-between hover:bg-slate-50/50 transition-colors">
                       <div class="flex items-center gap-4">
                          <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-400 uppercase">{{ item.student[0] }}</div>
                          <div>
                             <p class="text-sm font-bold text-slate-900">{{ item.student }}</p>
                             <p class="text-xs text-slate-400 font-medium">{{ item.detail }}</p>
                          </div>
                       </div>
                       <div class="text-right">
                          <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tighter">{{ item.date }}</span>
                          <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest ">Concluído</span>
                       </div>
                    </div>
                 </div>
                 <div v-if="recentActivity?.length === 0" class="p-8 text-center text-slate-400 text-sm italic font-medium">Nenhuma atividade recente no sistema.</div>
              </div>

              <!-- Agenda Escolar Dinâmica -->
              <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100 space-y-6 relative overflow-hidden flex flex-col justify-between">
                  <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                  <div>
                    <h4 class="text-xl font-bold flex items-center gap-2 relative z-10 mb-6">
                       <CalendarDays :size="24" />
                       Agenda Escolar
                    </h4>
                    <div v-if="(schoolEvents?.length ?? 0) > 0" class="space-y-4">
                       <div v-for="event in schoolEvents" :key="event.id" class="p-4 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10 flex items-center gap-4 group hover:bg-white/20 transition-all cursor-default relative">
                          <div class="w-12 h-12 rounded-xl bg-white/20 flex flex-col items-center justify-center shrink-0 shadow-inner">
                             <span class="text-[10px] font-black leading-none opacity-80">{{ event.month }}</span>
                             <span class="text-lg font-black leading-none mt-0.5">{{ event.day }}</span>
                          </div>
                          <div class="min-w-0">
                             <p class="text-sm font-bold truncate tracking-tight">{{ event.title }}</p>
                             <p class="text-[10px] text-white/60 font-medium truncate italic">{{ event.description }}</p>
                          </div>
                       </div>
                    </div>
                    <div v-else class="text-center py-10 border border-dashed border-white/20 rounded-2xl">
                       <p class="text-white/60 text-xs font-bold uppercase tracking-widest">Nenhum evento agendado</p>
                    </div>
                  </div>
                  <button class="w-full py-3 bg-white text-indigo-600 font-bold rounded-2xl hover:bg-slate-50 transition-all shadow-lg active:scale-95 mt-4">Ver Agenda Completa</button>
              </div>
           </div>
        </div>

        <!-- 2. TEACHER DASHBOARD -->
        <div v-if="isTeacher" class="space-y-8">
           <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             <div v-for="(val, label) in {
               'Minhas Turmas': {v: stats.myClasses, icon: Layers, color: 'indigo'},
               'Meus Alunos': {v: stats.myStudents, icon: GraduationCap, color: 'emerald'},
               'Avaliações Planejadas': {v: stats.assessmentsPlanned, icon: PenTool, color: 'amber'}
             }" :key="label" 
             class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20 group hover:shadow-md transition-all h-full"
             >
                <div :class="`w-14 h-14 rounded-2xl bg-${val.color}-50 flex items-center justify-center text-${val.color}-600 mb-6 group-hover:scale-105 transition-transform`">
                   <component :is="val.icon" :size="32" />
                </div>
                <div class="space-y-1">
                   <p class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ label }}</p>
                   <p class="text-4xl font-black text-slate-800 tracking-tight">{{ val.v }}</p>
                </div>
             </div>
           </div>

           <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Pending Attendance -->
              <div class="bg-amber-50 rounded-3xl p-8 border border-amber-100 flex items-start gap-5 relative overflow-hidden group">
                 <div class="absolute -right-4 -top-4 w-32 h-32 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                 <div class="w-14 h-14 rounded-2xl bg-amber-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-amber-200">
                    <AlertCircle :size="32" />
                 </div>
                 <div class="space-y-4">
                    <div>
                       <h3 class="text-xl font-bold text-amber-900 leading-tight">Chamadas do Dia</h3>
                       <p class="text-amber-700/70 text-sm font-medium mt-1">Lembre-se de fechar a frequência hoje.</p>
                    </div>
                    <div v-if="(pendingAttendance?.length ?? 0) > 0" class="space-y-2">
                       <p class="text-xs font-black text-amber-600/80 uppercase tracking-widest">Pendências:</p>
                       <div v-for="item in pendingAttendance" :key="item" class="flex items-center gap-2 text-amber-900 font-bold text-sm bg-white/50 px-3 py-2 rounded-xl border border-amber-200/50">
                          <Clock :size="14" /> {{ item }}
                       </div>
                    </div>
                    <div v-else class="flex items-center gap-2 text-emerald-600 font-bold text-sm bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100">
                       <CheckCircle2 :size="16" /> Tudo em dia por hoje!
                    </div>
                    <Link :href="route('attendance.index')" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-2xl text-sm font-bold transition-all shadow-lg active:scale-95">
                       Ir para Chamada <ChevronRight :size="16" />
                    </Link>
                 </div>
              </div>

              <!-- Teacher Links List -->
              <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm ring-1 ring-slate-200/20 flex flex-col justify-between">
                 <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                       <BookOpen class="text-indigo-600" :size="24" />
                       Minhas Disciplinas
                    </h3>
                    <div class="space-y-3">
                       <div v-for="link in myLinks?.slice(0, 3)" :key="link.id" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/50 rounded-2xl transition-all group">
                          <div class="flex items-center gap-4">
                             <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center font-bold text-slate-400 uppercase text-xs">
                                {{ link.subject.name.substring(0, 2) }}
                             </div>
                             <div>
                                <p class="text-sm font-bold text-slate-800">{{ link.subject.name }}</p>
                                <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">{{ link.school_class.name }}</p>
                             </div>
                          </div>
                          <Link :href="route('attendance.index', {link_id: link.id})" class="p-2 rounded-lg group-hover:bg-indigo-600 group-hover:text-white text-slate-300 transition-all">
                             <ChevronRight :size="18" />
                          </Link>
                       </div>
                    </div>
                 </div>
                 <Link :href="route('attendance.index')" v-if="(myLinks?.length ?? 0) > 3" class="text-sm font-bold text-indigo-600 text-center mt-6 hover:underline flex items-center justify-center gap-1 group">
                    Ver todos os vínculos <ArrowUpRight :size="14" class="group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                 </Link>
              </div>
           </div>
        </div>

        <!-- 3. GUARDIAN DASHBOARD -->
        <div v-if="isGuardian" class="space-y-8">
           <!-- Children Overview -->
           <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2 px-2">
              <Baby class="text-indigo-600" :size="24" />
              Status Acadêmico dos Filhos
           </h3>
           <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <div v-for="child in myChildren" :key="child.id" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20 space-y-6 group hover:shadow-xl hover:shadow-indigo-50 transition-all">
                <div class="flex items-start justify-between">
                   <div class="flex items-center gap-4">
                      <div class="w-16 h-16 rounded-3xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-inner">
                         <Users :size="32" />
                      </div>
                      <div class="space-y-0.5">
                         <h4 class="text-2xl font-black text-slate-900 leading-tight">{{ child.name }}</h4>
                         <p class="text-sm font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-lg w-fit">{{ child.class }}</p>
                      </div>
                   </div>
                   <div class="flex items-center gap-1 bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl text-xs font-black">
                      <Target :size="14" /> {{ child.attendanceRate }}% Frequência
                   </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                   <Link :href="route('guardian.report-card', { student_id: child.id })" class="w-full py-4 bg-slate-50 hover:bg-indigo-600 hover:text-white rounded-2xl text-xs font-black uppercase text-center transition-all">Ver Boletim Completo</Link>
                   <Link :href="route('guardian.attendance', { student_id: child.id })" class="w-full py-4 bg-slate-50 hover:bg-indigo-600 hover:text-white rounded-2xl text-xs font-black uppercase text-center transition-all">Faltas por Bimestre</Link>
                </div>
             </div>
           </div>

           <!-- Upcoming Exams Agenda -->
           <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
              <h3 class="text-xl font-bold text-slate-900 mb-8 flex items-center gap-2">
                 <PenTool class="text-rose-500" :size="24" />
                 Agenda de Provas & Trabalhos
              </h3>
              
              <div v-if="(upcomingAssessments?.length ?? 0) > 0" class="space-y-4">
                 <div v-for="exam in upcomingAssessments" :key="exam.id" class="p-5 bg-slate-50/50 hover:bg-slate-100 transition-all rounded-3xl border border-slate-100 flex items-center justify-between group">
                    <div class="flex items-center gap-6">
                       <div class="w-12 h-12 bg-white rounded-2xl border border-slate-200 flex flex-col items-center justify-center shrink-0">
                          <span class="text-[10px] font-black leading-none text-slate-400">{{ new Date(exam.date).toLocaleDateString('pt-BR', {month: 'short'}).toUpperCase().replace('.', '') }}</span>
                          <span class="text-lg font-black leading-none mt-0.5 text-slate-800">{{ new Date(exam.date).getDate() }}</span>
                       </div>
                       <div>
                          <p class="text-sm font-bold text-slate-900">{{ exam.name }}</p>
                          <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ exam.class_subject.subject.name }} | {{ exam.class_subject.school_class.name }}</p>
                       </div>
                    </div>
                    <div class="text-right">
                       <span class="text-[10px] font-black text-slate-400 block mb-1 uppercase tracking-tight">Valor</span>
                       <span class="text-sm font-black text-indigo-600">{{ exam.max_score }} pts</span>
                    </div>
                 </div>
              </div>
              <div v-else class="text-center py-12 p-8 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                 <CalendarDays class="mx-auto text-slate-300 mb-3" :size="32" />
                 <p class="text-slate-500 font-bold">Nenhuma avaliação programada para os próximos dias.</p>
              </div>
           </div>
        </div>

      </div>
    </template>
  </AuthenticatedLayout>
</template>
