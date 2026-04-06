<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { 
  GraduationCap,
  ArrowRight,
  Eye,
  EyeOff
} from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <div class="min-h-screen bg-white flex items-center justify-center p-6 font-sans text-slate-800 antialiased">
    <Head title="Login" />

    <div class="w-full max-w-[400px] space-y-12 animate-in fade-in slide-in-from-bottom-4 duration-1000">
      
      <!-- Minimal Header -->
      <div class="flex flex-col items-center space-y-6">
        <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm">
           <GraduationCap class="text-white" :size="24" />
        </div>
        <div class="text-center space-y-1">
           <h1 class="text-2xl font-semibold tracking-tight text-slate-950">ClassFlow</h1>
           <p class="text-sm text-slate-400 font-medium">Insira suas credenciais para continuar</p>
        </div>
      </div>

      <!-- Main Form (Ultra Clean) -->
      <form @submit.prevent="submit" class="space-y-6">
        <div class="space-y-4">
           <!-- Email -->
           <div class="space-y-1.5 px-0.5">
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">E-mail</label>
              <input 
                v-model="form.email"
                type="email" 
                autocomplete="email"
                placeholder="nome@exemplo.com"
                :class="['w-full bg-white border rounded-xl py-3.5 px-4 text-sm font-medium focus:ring-4 transition-all outline-none', form.errors.email ? 'border-red-400 focus:border-red-400 focus:ring-red-400/10' : 'border-slate-200 focus:border-slate-900 focus:ring-slate-900/5']"
              >
              <InputError :message="form.errors.email" />
           </div>

           <!-- Password -->
           <div class="space-y-1.5 px-0.5">
              <div class="flex items-center justify-between">
                 <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Senha</label>
                 <Link class="text-[11px] font-bold text-slate-300 hover:text-slate-900 uppercase tracking-widest transition-colors">Esqueceu?</Link>
              </div>
              <div class="relative">
                 <input 
                   v-model="form.password"
                   :type="showPassword ? 'text' : 'password'" 
                   autocomplete="current-password"
                   placeholder="••••••••"
                   :class="['w-full bg-white border rounded-xl py-3.5 px-4 text-sm font-medium focus:ring-4 transition-all outline-none', form.errors.password ? 'border-red-400 focus:border-red-400 focus:ring-red-400/10' : 'border-slate-200 focus:border-slate-900 focus:ring-slate-900/5']"
                 >
                 <button 
                   type="button"
                   @click="showPassword = !showPassword"
                   class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors"
                 >
                    <Eye v-if="!showPassword" :size="18" />
                    <EyeOff v-else :size="18" />
                 </button>
              </div>
              <InputError :message="form.errors.password" />
           </div>
        </div>

        <div class="flex items-center gap-2.5 px-1 py-1">
           <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 transition-all cursor-pointer">
           <span class="text-xs font-semibold text-slate-400">Manter conectado</span>
        </div>

        <div class="pt-2">
           <button 
            type="submit"
            :disabled="form.processing"
            class="w-full bg-slate-950 hover:bg-slate-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-slate-900/10 transition-all active:scale-[0.98] flex items-center justify-center gap-2 group disabled:opacity-50"
           >
              <span>Entrar</span>
              <ArrowRight :size="16" class="group-hover:translate-x-1 transition-transform" />
           </button>
        </div>
      </form>

      <!-- Footer Info -->
      <div class="pt-10 text-center">
         <p class="text-[10px] font-black text-slate-200 uppercase tracking-[0.4em]">
            ClassFlow • ERP
         </p>
      </div>

    </div>
  </div>
</template>
