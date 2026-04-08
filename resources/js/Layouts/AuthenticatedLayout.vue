<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Calendar, 
  BookOpen, 
  Layers, 
  Users, 
  GraduationCap, 
  ClipboardCheck, 
  ClipboardList,
  ShieldCheck,
  Baby,
  BarChart3,
  CalendarDays,
  Menu,
  X,
  LogOut,
  ChevronRight,
  Search,
  Bell,
  ChevronDown,
  Settings,
  FolderOpen
} from 'lucide-vue-next';

interface MenuItem {
  name: string;
  icon: any;
  route?: string;
  component?: string;
  roles: string[];
  restricted?: boolean;
  children?: MenuItem[];
}

import { PageProps } from '@inertiajs/core';

interface AuthenticatedPageProps extends PageProps {
  auth: { 
    user: any; 
    hasActiveClasses: boolean;
  }
}

const isSidebarOpen = ref(true);
const openMenus = ref<string[]>([]);
const page = usePage<AuthenticatedPageProps>();

const toggleSidebar = () => isSidebarOpen.value = !isSidebarOpen.value;

const toggleSubmenu = (name: string) => {
  const index = openMenus.value.indexOf(name);
  if (index > -1) openMenus.value.splice(index, 1);
  else openMenus.value.push(name);
};

const userRoleNames: Record<string, string> = {
  admin: 'Diretoria',
  director: 'Diretoria',
  teacher: 'Professor(a)',
  guardian: 'Responsável',
};

const userRoleDisplayName = computed(() => {
  const role = page.props.auth.user?.role || 'user';
  return userRoleNames[role as string] || 'Usuário';
});

const userRoleSlug = computed(() => page.props.auth.user?.role || 'user');

const menuItems: MenuItem[] = [
  { name: 'Dashboard', icon: LayoutDashboard, route: 'dashboard', component: 'Dashboard', roles: ['admin', 'director', 'teacher', 'guardian'] },
  
  // Diretoria - Agrupado (Acadêmico)
  { 
    name: 'Acadêmico', 
    icon: BookOpen, 
    roles: ['admin', 'director'],
    children: [
      { name: 'Agenda Escolar', icon: CalendarDays, route: 'school-events.index', component: 'Events/Index', roles: ['admin', 'director'] },
      { name: 'Períodos Letivos', icon: CalendarDays, route: 'terms.index', component: 'AcademicYears/Terms', roles: ['admin', 'director'] },
      { name: 'Disciplinas', icon: BookOpen, route: 'subjects.index', component: 'Subjects/Index', roles: ['admin', 'director'] },
      { name: 'Turmas', icon: Layers, route: 'classes.index', component: 'Classes/Index', roles: ['admin', 'director'] },
    ]
  },

  // Diretoria - Agrupado (Gestão)
  {
    name: 'Gestão Escolar',
    icon: Users,
    roles: ['admin', 'director'],
    children: [
      { name: 'Professores', icon: Users, route: 'teachers.index', component: 'Teachers/Index', roles: ['admin', 'director'] },
      { name: 'Alunos', icon: GraduationCap, route: 'students.index', component: 'Students/Index', roles: ['admin', 'director'] },
      { name: 'Vínculos Docentes', icon: ShieldCheck, route: 'academic-links.index', component: 'Teachers/AcademicLinks', roles: ['admin', 'director'] },
      { name: 'Responsáveis', icon: Users, route: 'admin.users.index', component: 'Admin/Users/Index', roles: ['admin', 'director'] },
    ]
  },

  // Conselho de Classe - Isolado (Acesso restrito)
  { name: 'Conselho de Classe', icon: BarChart3, route: 'enrollments.review', component: 'Admin/Enrollments/Review', roles: ['admin', 'director'] },

  // Professor
  { 
    name: 'Chamada Diária', 
    icon: ClipboardCheck, 
    route: 'attendance.index', 
    component: 'Attendance/Index', 
    roles: ['teacher'],
    restricted: true 
  },
  { name: 'Meu Horário', icon: Calendar, route: 'teacher.schedule', component: 'Teacher/Schedule', roles: ['teacher'] },
  { name: 'Plano de Avaliação', icon: ClipboardList, route: 'assessments.index', component: 'Assessments/Index', roles: ['teacher'] },
  { 
    name: 'Lançar Notas', 
    icon: BarChart3, 
    route: 'grades.index', 
    component: 'Grades/Index', 
    roles: ['teacher'],
    restricted: true 
  },
  
  // Responsável
  { name: 'Boletim do Filho', icon: Baby, route: 'guardian.report-card', component: 'Guardian/ReportCard', roles: ['guardian'] },
];

const filteredMenu = computed(() => {
  return menuItems.filter(item => item.roles.includes(userRoleSlug.value));
});

const isCurrentRoute = (item: MenuItem) => {
  if (item.route) return route().current(item.route);
  if (item.children) return item.children.some(child => child.route && route().current(child.route));
  return false;
};

// Verificar se o conselho está aberto
const isCouncilOpen = computed(() => {
  const now = new Date();
  return now.getMonth() === 11 && now.getDate() >= 5 && now.getDate() <= 12;
});
</script>

<template>
  <div class="min-h-screen bg-[#F8FAFC] flex font-sans text-slate-900">
    <!-- Sidebar -->
    <aside 
      :class="['bg-white border-r border-slate-200 transition-all duration-300 flex flex-col z-50 sticky top-0 h-screen', isSidebarOpen ? 'w-64' : 'w-20']"
    >
      <div class="p-6 flex items-center justify-between h-20">
        <div v-if="isSidebarOpen" class="flex items-center gap-2 overflow-hidden animate-in fade-in slide-in-from-left-4">
          <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center shrink-0">
             <GraduationCap class="text-white" :size="20" />
          </div>
          <h1 class="text-xl font-bold tracking-tight text-slate-900 truncate">ClassFlow</h1>
        </div>
        <button @click="toggleSidebar" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
          <Menu v-if="!isSidebarOpen" :size="20" />
          <X v-else :size="20" />
        </button>
      </div>

      <nav class="flex-1 px-4 space-y-1.5 mt-4 overflow-y-auto overflow-x-hidden custom-scrollbar pb-10">
        <div v-for="item in filteredMenu" :key="item.name">
          <!-- Parent Item -->
          <div v-if="item.children && isSidebarOpen">
             <button 
               @click="toggleSubmenu(item.name)"
               :class="['flex items-center justify-between w-full p-3 rounded-xl transition-all duration-200', isCurrentRoute(item) ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900']"
             >
                <div class="flex items-center">
                   <component :is="item.icon" :size="20" />
                   <span class="ml-3 font-medium text-sm">{{ item.name }}</span>
                </div>
                <ChevronDown :size="14" :class="['transition-transform duration-300', openMenus.includes(item.name) ? 'rotate-180' : '']" />
             </button>
             
             <!-- Children -->
             <div v-show="openMenus.includes(item.name) || isCurrentRoute(item)" class="ml-9 mt-1 space-y-1 animate-in slide-in-from-top-2 duration-300">
                <Link v-for="child in item.children" :key="child.name" :href="route(child.route!)"
                  :class="['flex items-center p-2.5 rounded-lg text-xs font-semibold transition-all', route().current(child.route!) ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-400 hover:text-slate-700 hover:bg-slate-50']"
                >
                   {{ child.name }}
                </Link>
             </div>
          </div>

          <!-- Single Item -->
          <div v-else-if="!item.children || !isSidebarOpen" class="relative group">
             <Link 
               :href="item.route ? route(item.route) : '#'" 
               :class="[
                 'flex items-center p-3 rounded-xl transition-all duration-200',
                 (item.route && route().current(item.route)) ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900',
                 (item.name === 'Conselho de Classe' && !isCouncilOpen) ? 'opacity-50 grayscale cursor-not-allowed pointer-events-none' : '',
                 (item.restricted && !page.props.auth.hasActiveClasses) ? 'opacity-50 grayscale cursor-not-allowed pointer-events-none' : ''
               ]"
             >
                <component :is="item.icon" :size="20" />
                <span v-if="isSidebarOpen" class="ml-3 font-medium text-sm">{{ item.name }}</span>
                <div v-if="item.restricted && isSidebarOpen && !page.props.auth.hasActiveClasses" class="ml-auto text-[9px] font-black text-amber-500 bg-amber-50 px-1.5 py-0.5 rounded tracking-tighter uppercase whitespace-nowrap">Turmas Inativas</div>
                <div v-if="item.name === 'Conselho de Classe' && isSidebarOpen && !isCouncilOpen" class="ml-auto text-[9px] font-black text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded tracking-tighter uppercase">Bloqueado</div>
             </Link>
             
             <div v-if="!isSidebarOpen" class="absolute left-16 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50">
                {{ item.name }}
             </div>
          </div>
        </div>
      </nav>

      <div class="p-4 border-t border-slate-100 bg-white">
        <div class="flex items-center p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-100 shrink-0">
                CF
            </div>
            <div v-if="isSidebarOpen" class="ml-3 overflow-hidden">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ page.props.auth.user?.name || 'Convidado' }}</p>
                <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">{{ userRoleDisplayName }}</p>
            </div>
        </div>
        
        <Link as="button" method="post" :href="route('logout')" class="mt-4 flex items-center w-full p-3 text-rose-500 hover:bg-rose-50 rounded-xl transition-all duration-200 group">
          <LogOut :size="20" class="group-hover:-translate-x-1 transition-transform" />
          <span v-if="isSidebarOpen" class="ml-3 font-semibold text-sm">Encerrar Sessão</span>
        </Link>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
      <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 h-20 flex items-center justify-between sticky top-0 z-40">
         <div class="flex items-center space-x-3 text-sm text-slate-500">
            <span class="hover:text-indigo-600 transition-colors cursor-pointer font-bold">Unidade Escolar</span>
            <ChevronRight :size="14" class="text-slate-300" />
            <span class="font-bold text-slate-900">{{ page.component.split('/').pop() }}</span>
         </div>
         
         <div class="flex items-center space-x-6">
             <button class="relative p-2 rounded-xl hover:bg-slate-100 text-slate-500 transition-colors">
                <Bell :size="20" />
                <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-white"></span>
             </button>
         </div>
      </header>

      <main class="flex-1 p-8 max-w-7xl w-full mx-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
</style>
