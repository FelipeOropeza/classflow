<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
  CalendarDays, 
  Plus, 
  Trash2, 
  AlertCircle,
  CheckCircle2,
  Calendar,
  X
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
  events: any[];
  activeYearId: number;
}>();

const showModal = ref(false);

const form = useForm({
  title: '',
  description: '',
  event_date: '',
  type: 'event', // holiday, meeting, event
  academic_year_id: props.activeYearId
});

const submit = () => {
  form.post(route('school-events.store'), {
    onSuccess: () => {
      showModal.value = false;
      form.reset();
    }
  });
};

const deleteEvent = (id: number) => {
  if (confirm('Tem certeza que deseja remover este evento da agenda?')) {
    form.delete(route('school-events.destroy', id));
  }
};

const getEventBadge = (type: string) => {
  const styles: Record<string, string> = {
    holiday: 'text-rose-500 bg-rose-50 border-rose-100',
    meeting: 'text-amber-600 bg-amber-50 border-amber-100',
    event: 'text-indigo-600 bg-indigo-50 border-indigo-100'
  };
  return styles[type] || styles.event;
};

const getEventLabel = (type: string) => {
  const labels: Record<string, string> = {
    holiday: 'Feriado',
    meeting: 'Reunião',
    event: 'Evento'
  };
  return labels[type] || 'Evento';
};
</script>

<template>
  <Head title="Agenda Escolar" />

  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto space-y-10 py-4 animate-in fade-in duration-700">
      
      <!-- Minimalist Header -->
      <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-slate-100 pb-8 gap-6">
        <div class="space-y-1">
          <h2 class="text-2xl font-semibold text-slate-900 tracking-tight flex items-center gap-3">
             <CalendarDays :size="20" class="text-slate-400" />
             Agenda Escolar
          </h2>
          <p class="text-slate-400 text-sm italic ml-8">Cronograma de eventos e compromissos institucionais do período letivo.</p>
        </div>
        
        <button @click="showModal = true" class="flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all active:scale-95">
          <Plus :size="16" />
          Registrar Novo Evento
        </button>
      </div>

      <!-- Main Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Sidebar: Event Info (Clean) -->
        <div class="lg:col-span-4 space-y-8">
           <div class="bg-indigo-600 rounded-xl p-8 text-white space-y-5">
              <div class="space-y-1">
                 <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Status da Agenda</p>
                 <p class="text-3xl font-bold tracking-tight">{{ events.length }} Eventos Registrados</p>
              </div>
              <div class="h-px bg-white/10"></div>
              <div class="flex items-center gap-2 text-indigo-100 text-xs font-medium italic opacity-80">
                 <AlertCircle :size="14" />
                 Atualizado em tempo real para toda a comunidade.
              </div>
           </div>

           <!-- Legend -->
           <div class="bg-white border border-slate-100 rounded-xl p-6 space-y-4 shadow-sm shadow-slate-100/50">
              <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Codificação Visual</h5>
              <div class="space-y-2">
                 <div v-for="(val, key) in { holiday: 'Feriado e Recessos', meeting: 'Conselho e Reunião', event: 'Atividades em Geral' }" :key="key" class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100 transition-colors">
                    <div :class="`w-2 h-2 rounded-full ${getEventBadge(key).split(' ')[1]}`"></div>
                    <span class="text-[11px] font-bold text-slate-600">{{ val }}</span>
                 </div>
              </div>
           </div>
        </div>

        <!-- Main Timeline (Simplified) -->
        <div class="lg:col-span-8 space-y-3">
          <div v-if="events.length === 0" class="bg-white border border-dashed border-slate-200 rounded-xl p-20 flex flex-col items-center justify-center text-center space-y-4">
              <Calendar :size="32" class="text-slate-200" />
              <div>
                 <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Nenhum Registro</h4>
                 <p class="text-slate-300 text-xs mt-1">A linha do tempo do calendário está vazia por enquanto.</p>
              </div>
          </div>

          <div v-for="event in events" :key="event.id" class="bg-white border border-slate-100 p-5 rounded-xl flex items-center justify-between hover:border-slate-200 transition-all group shadow-sm shadow-slate-100/30">
            <div class="flex items-center gap-6">
                <!-- Data Chip -->
                <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-lg flex flex-col items-center justify-center shrink-0">
                   <span class="text-[9px] font-black uppercase text-slate-400 leading-none">{{ new Date(event.event_date).toLocaleDateString('pt-BR', {month: 'short'}).replace('.', '') }}</span>
                   <span class="text-lg font-bold text-slate-800 leading-none mt-0.5 tracking-tighter">{{ new Date(event.event_date).getDate() }}</span>
                </div>

                <div class="space-y-1">
                   <div class="flex items-center gap-2.5">
                      <h3 class="text-base font-bold text-slate-900 tracking-tight leading-none group-hover:text-indigo-600 transition-colors">{{ event.title }}</h3>
                      <span :class="`px-2 py-0.5 rounded text-[9px] font-bold uppercase border tracking-tight ${getEventBadge(event.type)}`">{{ getEventLabel(event.type) }}</span>
                   </div>
                   <p class="text-[12px] font-medium text-slate-400 italic leading-relaxed">{{ event.description || 'Nenhum detalhe adicional informado.' }}</p>
                </div>
            </div>

            <button @click="deleteEvent(event.id)" class="p-2.5 text-slate-200 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all opacity-0 group-hover:opacity-100 focus:opacity-100">
               <Trash2 :size="16" />
            </button>
          </div>
        </div>
      </div>

      <!-- Simplified Add Event Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
         <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8 space-y-8 animate-in zoom-in-95 duration-200 border border-slate-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-900">Novo Evento</h3>
                <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition-colors bg-slate-50 rounded-lg">
                   <X :size="18" />
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
               <div class="space-y-1.5">
                  <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Título do Evento</label>
                  <input v-model="form.title" type="text" placeholder="Ex: Conselho de Classe" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-4 px-5 text-slate-800 font-bold placeholder:text-slate-300 focus:ring-1 focus:ring-indigo-600 transition-all outline-none" required />
               </div>

               <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-1.5">
                     <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Data</label>
                     <input v-model="form.event_date" type="date" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-4 px-5 text-slate-800 font-bold focus:ring-1 focus:ring-indigo-600 transition-all outline-none" required />
                  </div>
                  <div class="space-y-1.5">
                     <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Categoria</label>
                     <select v-model="form.type" class="w-full bg-slate-50 border border-slate-100 rounded-xl py-4 px-5 text-slate-800 font-bold focus:ring-1 focus:ring-indigo-600 transition-all outline-none appearance-none">
                        <option value="event">Evento Comum</option>
                        <option value="holiday">Feriado/Recesso</option>
                        <option value="meeting">Reunião Docente</option>
                     </select>
                  </div>
               </div>

               <div class="space-y-1.5">
                  <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Descrição</label>
                  <textarea v-model="form.description" rows="3" placeholder="Detalhes (opcional)..." class="w-full bg-slate-50 border border-slate-100 rounded-xl py-4 px-5 text-slate-800 font-bold placeholder:text-slate-300 focus:ring-1 focus:ring-indigo-600 transition-all outline-none resize-none"></textarea>
               </div>

               <button type="submit" :disabled="form.processing" class="w-full py-4 bg-slate-900 hover:bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-xl transition-all active:scale-95 flex items-center justify-center gap-3 disabled:opacity-50">
                  <span v-if="form.processing">Processando...</span>
                  <span v-else class="flex items-center gap-2">Salvar na Agenda <CheckCircle2 :size="18" /></span>
               </button>
            </form>
         </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
