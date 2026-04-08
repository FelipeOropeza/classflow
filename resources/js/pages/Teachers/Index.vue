<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  Users, 
  Plus, 
  Search, 
  MoreHorizontal, 
  Mail, 
  User, 
  Lock,
  X,
  Trash2,
  Settings2,
  ArrowRight
} from 'lucide-vue-next';

const props = defineProps<{
  teachers: any[];
}>();

const isModalOpen = ref(false);
const searchQuery = ref('');

const form = useForm({
  name: '',
  email: '',
  password: '',
});

const openModal = () => isModalOpen.value = true;
const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submit = () => {
  form.post(route('teachers.store'), {
    onSuccess: () => closeModal(),
  });
};

const deleteTeacher = (id: number) => {
  if (confirm('Tem certeza que deseja remover este professor?')) {
    form.delete(route('teachers.destroy', id));
  }
};

const alert = (msg: string) => window.alert(msg);
</script>

<template>
  <Head title="Professores" />

  <AuthenticatedLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
        <div class="flex items-center gap-5">
          <div class="w-16 h-16 rounded-[24px] bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
            <Users :size="32" />
          </div>
          <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Professores</h1>
            <p class="text-slate-500 font-bold flex items-center gap-2">
              {{ teachers.length }} docentes ativos no sistema
            </p>
          </div>
        </div>
        
        <button 
          @click="openModal"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group"
        >
          <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" />
          <span>Novo Professor</span>
        </button>
      </div>

      <!-- Content Section -->
      <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
        <!-- Table Actions -->
        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex flex-col md:flex-row gap-4 justify-between items-center">
          <div class="relative w-full md:w-96 group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors" :size="18" />
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Buscar por nome ou email..."
              class="w-full pl-12 pr-4 py-3 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:ring-4 focus:ring-indigo-600/5 transition-all"
            >
          </div>
        </div>

        <!-- Teachers Table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-slate-50/50">
                <th class="px-8 py-5 text-left text-[11px] font-black uppercase tracking-widest text-slate-400">Docente</th>
                <th class="px-8 py-5 text-left text-[11px] font-black uppercase tracking-widest text-slate-400">Email</th>
                <th class="px-8 py-5 text-left text-[11px] font-black uppercase tracking-widest text-slate-400">Cadastro</th>
                <th class="px-8 py-5 text-right text-[11px] font-black uppercase tracking-widest text-slate-400">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
              <tr v-for="teacher in teachers" :key="teacher.id" class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-8 py-5">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                      {{ teacher.name.charAt(0) }}
                    </div>
                    <span class="font-bold text-slate-900">{{ teacher.name }}</span>
                  </div>
                </td>
                <td class="px-8 py-5">
                  <div class="flex items-center gap-2 text-slate-500 font-medium">
                    <Mail :size="14" />
                    {{ teacher.email }}
                  </div>
                </td>
                <td class="px-8 py-5">
                  <span class="text-sm font-bold text-slate-500">
                    {{ new Date(teacher.created_at).toLocaleDateString('pt-BR') }}
                  </span>
                </td>
                <td class="px-8 py-5 text-right">
                  <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="deleteTeacher(teacher.id)" class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                      <Trash2 :size="18" />
                    </button>
                    <button @click="alert('Módulo de edição de professor em desenvolvimento.')" class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Editar Docente">
                      <Settings2 :size="18" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="teachers.length === 0">
                <td colspan="4" class="px-8 py-20 text-center">
                  <div class="flex flex-col items-center gap-4">
                    <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                      <Users :size="40" />
                    </div>
                    <p class="text-slate-400 font-bold">Nenhum professor cadastrado ainda.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Teacher Modal -->
      <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
        
        <div class="relative bg-white w-full max-w-md rounded-[32px] shadow-2xl overflow-hidden scale-in">
          <div class="p-8">
            <div class="flex items-center justify-between mb-8">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                  <Plus :size="24" />
                </div>
                <div>
                  <h3 class="text-xl font-black text-slate-900 leading-tight">Novo Professor</h3>
                  <p class="text-sm font-medium text-slate-500">Cadastre o docente.</p>
                </div>
              </div>
              <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-900 transition-colors">
                <X :size="20" />
              </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
              <!-- Name -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Nome Completo</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <User :size="18" />
                  </div>
                  <input 
                    v-model="form.name"
                    type="text" 
                    required
                    placeholder="Ex: João Silva"
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                </div>
              </div>

              <!-- Email -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Email Profissional</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Mail :size="18" />
                  </div>
                  <input 
                    v-model="form.email"
                    type="email" 
                    required
                    placeholder="joao@escola.com"
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                </div>
              </div>

              <!-- Password -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Senha Provisória</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Lock :size="18" />
                  </div>
                  <input 
                    v-model="form.password"
                    type="password" 
                    required
                    placeholder="Mínimo 8 caracteres"
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                </div>
              </div>

              <div class="pt-6 flex items-center justify-end gap-3">
                <button 
                  type="button"
                  @click="closeModal"
                  class="px-6 py-3 rounded-xl font-black text-slate-500 hover:bg-slate-50 transition-all active:scale-95 text-xs uppercase"
                >
                  Cancelar
                </button>
                <button 
                  type="submit"
                  :disabled="form.processing"
                  class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-8 py-3 rounded-xl font-black shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2 text-xs uppercase tracking-widest"
                >
                  <Plus v-if="!form.processing" :size="16" />
                  <span>{{ form.processing ? 'Salvando...' : 'Cadastrar' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.scale-in {
  animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
