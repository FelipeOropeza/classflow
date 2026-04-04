<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
  Calendar, 
  Save, 
  CalendarDays,
  Clock,
  LayoutGrid,
  CheckCircle2,
  AlertCircle
} from 'lucide-vue-next';
import { ref } from 'vue';

interface Term {
  id: number;
  name: string;
  start_date: string;
  end_date: string;
}

const props = defineProps<{
  terms: Term[];
  activeYear: { name: string };
}>();

const editingTerm = ref<number | null>(null);

const form = useForm({
  start_date: '',
  end_date: ''
});

const startEdit = (term: Term) => {
  editingTerm.value = term.id;
  form.start_date = term.start_date;
  form.end_date = term.end_date;
};

const cancelEdit = () => {
  editingTerm.value = null;
  form.reset();
};

const submit = (termId: number) => {
  form.patch(route('terms.update', termId), {
    onSuccess: () => cancelEdit()
  });
};
</script>

<template>
  <Head title="Gerenciar Bimestres" />

  <AuthenticatedLayout>
    <template #default>
      <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
              <div class="p-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                <Calendar class="text-white" :size="24" />
              </div>
              Prazos dos Bimestres
            </h2>
            <p class="text-slate-500 mt-2 font-medium">Configure as janelas temporais para o ano letivo {{ activeYear.name }}.</p>
          </div>
        </div>

        <!-- Info Card -->
        <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
           <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
           <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
             <LayoutGrid :size="40" />
           </div>
           <div class="space-y-2 relative z-10 text-center md:text-left">
              <h3 class="text-2xl font-bold">Calendário Institucional</h3>
              <p class="text-indigo-100 max-w-xl font-medium">As datas definidas aqui bloqueiam automaticamente as frequências e as permissões de planejamento pedagógico dos professores, garantindo o rigor do cronograma escolar.</p>
           </div>
        </div>

        <!-- Terms List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in slide-in-from-bottom duration-500">
           <div v-for="term in terms" :key="term.id" 
                class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20 transition-all hover:shadow-md group relative overflow-hidden"
           >
              <div class="flex items-start justify-between mb-8">
                 <div class="space-y-1">
                    <span class="text-xs font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-3 py-1 rounded-lg">Fase {{ term.id }}</span>
                    <h4 class="text-2xl font-bold text-slate-900">{{ term.name }}</h4>
                 </div>
                 <button 
                  v-if="editingTerm !== term.id"
                  @click="startEdit(term)" 
                  class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-600 hover:text-white text-slate-400 transition-all"
                 >
                    <Save :size="20" />
                 </button>
              </div>

              <!-- View Mode -->
              <div v-if="editingTerm !== term.id" class="grid grid-cols-2 gap-8">
                <div class="space-y-1 text-center md:text-left">
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 justify-center md:justify-start">
                    <Clock :size="12" /> Início
                  </p>
                  <p class="text-lg font-black text-slate-700 font-mono">{{ term.start_date }}</p>
                </div>
                <div class="space-y-1 text-center md:text-left border-l border-slate-100 pl-8">
                  <p class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 justify-center md:justify-start">
                    <CalendarDays :size="12" /> Término
                  </p>
                  <p class="text-lg font-black text-slate-700 font-mono">{{ term.end_date }}</p>
                </div>
              </div>

              <!-- Edit Mode -->
              <form v-else @submit.prevent="submit(term.id)" class="space-y-6 animate-in zoom-in-95 duration-200">
                <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-indigo-600 uppercase ml-1">Data Início</label>
                    <input 
                      v-model="form.start_date"
                      type="date" 
                      class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                    >
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-indigo-600 uppercase ml-1">Data Fim</label>
                    <input 
                      v-model="form.end_date"
                      type="date" 
                      class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                    >
                  </div>
                </div>

                <div class="flex gap-2">
                  <button 
                    type="submit"
                    :disabled="form.processing"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-2xl transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2"
                  >
                    <CheckCircle2 :size="18" />
                    Salvar
                  </button>
                  <button 
                    type="button"
                    @click="cancelEdit"
                    class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-500 font-bold transition-all"
                  >
                    Cancelar
                  </button>
                </div>
              </form>
           </div>
        </div>

        <!-- System Alert -->
        <div class="p-6 bg-slate-100/50 rounded-3xl border border-slate-200/50 text-slate-500 text-sm font-medium flex items-start gap-4 leading-relaxed ring-1 ring-white/50">
           <AlertCircle class="text-indigo-400 shrink-0 mt-0.5" :size="20" />
           <p>
             <b>Atenção do Administrador:</b> Alterar os prazos dos bimestres impacta diretamente a disponibilidade dos formulários para os professores. Recomendamos realizar essas alterações apenas antes do início do ano letivo ou em casos de ajuste no calendário escolar oficial.
           </p>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
