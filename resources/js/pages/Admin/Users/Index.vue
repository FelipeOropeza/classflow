<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
  Users, 
  UserPlus, 
  Mail, 
  Shield, 
  Key,
  Search,
  MoreVertical,
  CheckCircle2
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  users: any[];
}>();

const showCreateModal = ref(false);

const form = useForm({
  name: '',
  email: '',
  password: 'password123', // Senha padrão sugerida
  role: 'guardian', // Padrão é responsável
});

const submit = () => {
  form.post(route('admin.users.store'), {
    onSuccess: () => {
      showCreateModal.value = false;
      form.reset();
    },
  });
};

const getRoleBadge = (role: string) => {
  if (role === 'admin') return 'text-rose-600 bg-rose-50 border-rose-100';
  if (role === 'teacher') return 'text-indigo-600 bg-indigo-50 border-indigo-100';
  return 'text-emerald-600 bg-emerald-50 border-emerald-100';
};
</script>

<template>
  <Head title="Gestão de Usuários" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-8 py-6 animate-in fade-in duration-700">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-1">
        <div class="space-y-1">
          <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
             <div class="p-2 bg-indigo-600 rounded-xl shadow-lg border border-indigo-700">
                <Users class="text-white" :size="24" />
             </div>
             Diretório de Responsáveis
          </h2>
          <p class="text-slate-400 text-sm font-medium italic">Cadastre e gerencie os dados de acesso para os pais e responsáveis legais.</p>
        </div>

        <button 
          @click="showCreateModal = true"
          class="flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all shadow-xl active:scale-95"
        >
          <UserPlus :size="16" /> Novo Responsável
        </button>
      </div>

      <!-- Users Grid/Table -->
      <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden ring-1 ring-slate-200/20">
         <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
               <thead>
                  <tr class="bg-slate-50/50">
                     <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nome Completo</th>
                     <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">E-mail de Acesso</th>
                     <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Nível de Acesso</th>
                     <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Cadastrado em</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-slate-100">
                  <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/20 transition-colors group">
                     <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                           <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-400 text-xs">{{ user.name[0] }}</div>
                           <span class="text-sm font-bold text-slate-900">{{ user.name }}</span>
                        </div>
                     </td>
                     <td class="px-8 py-6 text-center">
                        <span class="text-xs font-medium text-slate-500">{{ user.email }}</span>
                     </td>
                     <td class="px-8 py-6 text-center">
                        <span :class="['px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border', getRoleBadge(user.role)]">
                           {{ user.role }}
                        </span>
                     </td>
                     <td class="px-8 py-6 text-right">
                        <span class="text-[10px] font-bold text-slate-300 uppercase leading-none">{{ new Date(user.created_at).toLocaleDateString('pt-BR') }}</span>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>

      <!-- Single Modal Creation -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
         <div class="w-full max-w-xl bg-white rounded-[32px] p-10 shadow-2xl space-y-8 animate-in zoom-in-95 duration-300 translate-y-0">
            <div class="flex items-center justify-between">
               <h3 class="text-xl font-bold text-slate-900 tracking-tight">Novo Acesso</h3>
               <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">Fechar</button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
               <div class="space-y-4">
                  <div class="space-y-2">
                     <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nome Completo</label>
                     <div class="relative group">
                        <input v-model="form.name" type="text" placeholder="Ex: João Silva" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 pl-10 text-xs font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                        <Users class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" :size="16" />
                     </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                     <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">E-mail</label>
                        <div class="relative group">
                           <input v-model="form.email" type="email" placeholder="pai@exemplo.com" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 pl-10 text-xs font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                           <Mail class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" :size="16" />
                        </div>
                     </div>
                     <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Senha Inicial</label>
                        <div class="relative group">
                           <input v-model="form.password" type="password" required class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-3.5 pl-10 text-xs font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                           <Key class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" :size="16" />
                        </div>
                     </div>
                  </div>
               </div>

               <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-slate-900 text-white font-bold py-4 rounded-2xl transition-all shadow-xl shadow-indigo-100 active:scale-95 flex items-center justify-center gap-2">
                  <CheckCircle2 v-if="!form.processing" :size="18" />
                  <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  {{ form.processing ? 'Registrando...' : 'Finalizar Registro' }}
               </button>
            </form>
         </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
