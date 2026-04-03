<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { GraduationCap } from 'lucide-vue-next';
import { 
  ArrowRight, 
  ShieldCheck, 
  CheckCircle2,
  Lock,
  Mail,
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
  <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6 font-sans">
    <Head title="Acesso ao Sistema" />

    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl shadow-slate-200/50 flex overflow-hidden lg:min-h-[640px]">
      
      <!-- Branding Side (Left) -->
      <div class="hidden lg:flex lg:w-1/2 bg-indigo-600 relative overflow-hidden flex-col justify-between p-12 text-white group">
        <!-- Background Patterns -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-700 opacity-90"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-900/40 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shadow-lg transform group-hover:rotate-12 transition-transform duration-500">
                    <GraduationCap class="text-indigo-600" :size="28" />
                </div>
                <h1 class="text-3xl font-black tracking-tight">ClassFlow</h1>
            </div>
            
            <div class="space-y-4">
                <h2 class="text-4xl font-extrabold leading-tight tracking-tighter">
                   Gerencie sua escola de ponta a ponta.
                </h2>
                <p class="text-lg font-medium text-indigo-100/80 leading-relaxed max-w-sm">
                   A plataforma integrada para diário de classe, notas, frequências e controle administrativo premium.
                </p>
            </div>
        </div>

        <div class="relative z-10 bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-3xl shadow-inner">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-full bg-emerald-400 flex items-center justify-center shadow-lg shadow-emerald-400/30">
                    <CheckCircle2 class="text-white" :size="20" />
                </div>
                <div>
                    <h4 class="font-bold text-sm">Controle Total</h4>
                    <p class="text-xs text-indigo-100">Painéis inteligentes para Diretores e Professores.</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-400 flex items-center justify-center shadow-lg shadow-amber-400/30">
                    <ShieldCheck class="text-white" :size="20" />
                </div>
                <div>
                    <h4 class="font-bold text-sm">Segurança de Dados</h4>
                    <p class="text-xs text-indigo-100">Criptografia de ponta a ponta e RBAC Integrado.</p>
                </div>
            </div>
        </div>
      </div>

      <!-- Form Side (Right) -->
      <div class="flex-1 p-8 lg:p-20 flex flex-col justify-center">
        <div class="mb-10 text-center lg:text-left">
           <h3 class="text-3xl font-black text-slate-900 tracking-tight">Login</h3>
           <p class="text-slate-500 font-semibold mt-2 tracking-wide">Entre com suas credenciais de acesso.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
           <div class="space-y-2 group">
              <label class="text-xs font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors">E-mail Corporativo</label>
              <div class="relative">
                 <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                    <Mail :size="20" />
                 </div>
                 <input 
                   v-model="form.email"
                   type="email" 
                   required
                   placeholder="exemplo@classflow.com"
                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-semibold text-slate-900 outline-none focus:border-indigo-600/20 focus:ring-4 focus:ring-indigo-600/5 transition-all duration-300 placeholder:text-slate-300"
                 >
              </div>
           </div>

           <div class="space-y-2 group">
              <label class="text-xs font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors">Sua Senha</label>
              <div class="relative">
                 <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                    <Lock :size="20" />
                 </div>
                 <input 
                   v-model="form.password"
                   :type="showPassword ? 'text' : 'password'" 
                   required
                   placeholder="••••••••"
                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-12 text-sm font-semibold text-slate-900 outline-none focus:border-indigo-600/20 focus:ring-4 focus:ring-indigo-600/5 transition-all duration-300 placeholder:text-slate-300"
                 >
                 <button 
                   type="button"
                   @click="showPassword = !showPassword"
                   class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600"
                 >
                    <Eye v-if="!showPassword" :size="20" />
                    <EyeOff v-else :size="20" />
                 </button>
              </div>
           </div>

           <div class="flex items-center justify-between py-2">
              <label class="flex items-center cursor-pointer group">
                 <input type="checkbox" v-model="form.remember" class="w-5 h-5 rounded-lg border-2 border-slate-200 text-indigo-600 focus:ring-indigo-600-inset transition-all">
                 <span class="ml-3 text-sm font-bold text-slate-500 group-hover:text-slate-700">Lembrar de mim</span>
              </label>
              <Link class="text-sm font-black text-indigo-600 hover:text-indigo-800 tracking-tight transition-colors">Esqueceu a senha?</Link>
           </div>

           <button 
              type="submit"
              :disabled="form.processing"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-3 group relative overflow-hidden"
           >
              <div class="absolute inset-0 bg-white/10 -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
              <span class="relative z-10">Entrar no Sistema</span>
              <ArrowRight class="relative z-10 group-hover:translate-x-1 transition-transform" :size="20" />
           </button>
        </form>

        <div class="mt-12 text-center text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">
            © 2026 ClassFlow ERP • Eficiência Pedagógica
        </div>
      </div>
    </div>
  </div>
</template>
