<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  Users, 
  GraduationCap, 
  BookOpen, 
  Calendar,
  LogOut,
  ChevronRight,
  Menu,
  X,
  Bell,
  Search,
  Layers,
  ShieldCheck
} from 'lucide-vue-next';

interface MenuItem {
  name: string;
  icon: any;
  route: string;
  component: string;
}

import { Auth } from '@/types/auth';
import { PageProps } from '@inertiajs/core';

interface AuthenticatedPageProps extends PageProps {
  auth: {
    user: any;
  }
}

const isSidebarOpen = ref(true);
const page = usePage<AuthenticatedPageProps>();

const toggleSidebar = () => isSidebarOpen.value = !isSidebarOpen.value;

const roleNames: Record<string, string> = {
  director: 'Diretor(a)',
  teacher: 'Professor(a)',
  guardian: 'Responsável',
  admin: 'Administrador',
};

const userRole = computed(() => {
  const role = (page.props.auth as any)?.user?.role || 'user';
  return roleNames[role as string] || 'Usuário';
});

const menuItems: MenuItem[] = [
  { name: 'Dashboard', icon: LayoutDashboard, route: 'dashboard', component: 'Dashboard' },
  { name: 'Ano Letivo', icon: Calendar, route: 'academic-years.index', component: 'AcademicYears/Index' },
  { name: 'Disciplinas', icon: BookOpen, route: 'subjects.index', component: 'Subjects/Index' },
  { name: 'Turmas', icon: Layers, route: 'classes.index', component: 'Classes/Index' },
  { name: 'Professores', icon: Users, route: 'teachers.index', component: 'Teachers/Index' },
  { name: 'Alunos', icon: GraduationCap, route: 'students.index', component: 'Students/Index' },
  { name: 'Vínculos', icon: ShieldCheck, route: 'academic-links.index', component: 'Teachers/AcademicLinks' },
];
</script>

<template>
  <div class="min-h-screen bg-[#F8FAFC] flex font-sans text-slate-900">
    <!-- Sidebar -->
    <aside 
      :class="[
        'bg-white border-r border-slate-200 transition-all duration-300 flex flex-col z-50 sticky top-0 h-screen',
        isSidebarOpen ? 'w-64' : 'w-20'
      ]"
    >
      <div class="p-6 flex items-center justify-between h-20">
        <div v-if="isSidebarOpen" class="flex items-center gap-2 overflow-hidden">
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

      <nav class="flex-1 px-4 space-y-1.5 mt-4 overflow-y-auto overflow-x-hidden custom-scrollbar">
        <Link 
          v-for="item in menuItems" 
          :key="item.name"
          :href="route(item.route)" 
          :class="[
            'flex items-center p-3 rounded-xl transition-all duration-200 group relative',
            $page.component === item.component || route().current(item.route) ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900'
          ]"
        >
          <component :is="item.icon" :size="20" :stroke-width="($page.component === item.component || route().current(item.route)) ? 2.5 : 2" />
          <span v-if="isSidebarOpen" class="ml-3 font-medium text-sm transition-opacity duration-300">{{ item.name }}</span>
          <div v-if="!isSidebarOpen" class="absolute left-16 bg-slate-900 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
             {{ item.name }}
          </div>
        </Link>
      </nav>

      <div class="p-4 border-t border-slate-100 bg-white">
        <div class="flex items-center p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-100 shrink-0">
                CF
            </div>
            <div v-if="isSidebarOpen" class="ml-3 overflow-hidden">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ page.props.auth?.user?.name || 'Convidado' }}</p>
                <p class="text-xs text-slate-500 font-medium">{{ userRole }}</p>
            </div>
        </div>
        
        <Link 
          as="button"
          method="post"
          :href="route('logout')"
          class="mt-4 flex items-center w-full p-3 text-rose-500 hover:bg-rose-50 rounded-xl transition-all duration-200 group"
        >
          <LogOut :size="20" class="group-hover:-translate-x-1 transition-transform" />
          <span v-if="isSidebarOpen" class="ml-3 font-semibold text-sm">Encerrar Sessão</span>
        </Link>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0">
      <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 h-20 flex items-center justify-between sticky top-0 z-40">
         <div class="flex items-center space-x-3 text-sm text-slate-500">
            <span class="hover:text-indigo-600 transition-colors cursor-pointer font-medium">Geral</span>
            <ChevronRight :size="14" class="text-slate-300" />
            <span class="font-semibold text-slate-900">Painel de Controle</span>
         </div>
         
         <div class="flex items-center space-x-6">
             <div class="relative hidden md:block">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" :size="16" />
                <input 
                  type="text" 
                  placeholder="Pesquisar..." 
                  class="bg-slate-100 border-none rounded-xl pl-10 pr-4 py-2 text-sm w-64 focus:ring-2 focus:ring-indigo-500/20 transition-all outline-none"
                >
             </div>
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
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #E2E8F0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #CBD5E1;
}
</style>
