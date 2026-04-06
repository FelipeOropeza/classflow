<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { 
  UserPlus, 
  Baby, 
  CalendarDays, 
  ArrowLeft,
  Save,
  ShieldCheck
} from 'lucide-vue-next';

const form = useForm({
  name: '',
  birth_date: '',
});

const submit = () => {
  form.post(route('guardian.student.store'));
};
</script>

<template>
  <Head title="Cadastrar Aluno" />

  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto py-12 animate-in fade-in duration-700">
      <!-- Header -->
      <div class="mb-10 space-y-4">
        <Link :href="route('dashboard')" class="flex items-center gap-1.5 text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:text-indigo-600 transition-all group w-fit">
          <ArrowLeft :size="12" class="group-hover:-translate-x-0.5 transition-transform" /> Voltar ao Painel
        </Link>
        <div class="flex items-center gap-4">
          <div class="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-100">
            <UserPlus class="text-white" :size="24" />
          </div>
          <div>
            <h2 class="text-2xl font-semibold text-slate-900 tracking-tight">Cadastrar Dependente</h2>
            <p class="text-slate-400 text-sm font-medium">Informe os dados básicos para iniciar o processo de matrícula.</p>
          </div>
        </div>
      </div>

      <!-- Form Card -->
      <div class="bg-white border border-slate-100 rounded-[32px] p-10 shadow-sm ring-1 ring-slate-200/20">
        <form @submit.prevent="submit" class="space-y-8">
          
          <div class="space-y-3">
             <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                Nome Completo do Aluno
             </label>
             <div class="relative group">
                <input 
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: João da Silva Santos"
                  required
                  class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 pl-12 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                >
                <Baby class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors" :size="20" />
             </div>
             <p v-if="form.errors.name" class="text-xs font-bold text-rose-500 ml-1 mt-1">{{ form.errors.name }}</p>
          </div>

          <div class="space-y-3">
             <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                Data de Nascimento
             </label>
             <div class="relative group">
                <input 
                  v-model="form.birth_date"
                  type="date"
                  required
                  class="w-full bg-slate-50 border-slate-200 rounded-2xl px-5 py-4 pl-12 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                >
                <CalendarDays class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors" :size="20" />
             </div>
             <p v-if="form.errors.birth_date" class="text-xs font-bold text-rose-500 ml-1 mt-1">{{ form.errors.birth_date }}</p>
          </div>

          <!-- Info Box -->
          <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 flex items-start gap-4">
             <ShieldCheck class="text-slate-400 shrink-0 mt-0.5" :size="20" />
             <div class="space-y-1">
                <p class="text-xs font-bold text-slate-700">O que acontece agora?</p>
                <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                   Após o cadastro, o registro ficará pendente na secretaria escolar. A diretoria será responsável por alocar seu filho em uma turma específica.
                </p>
             </div>
          </div>

          <div class="pt-4">
            <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-8 rounded-2xl transition-all shadow-lg shadow-slate-100 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50"
            >
              <Save v-if="!form.processing" :size="20" />
              <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              {{ form.processing ? 'Processando...' : 'Finalizar Cadastro' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
