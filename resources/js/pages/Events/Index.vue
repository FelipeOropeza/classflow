<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { 
  CalendarDays, 
  Plus, 
  Trash2, 
  Clock, 
  Tag, 
  AlertCircle,
  CheckCircle2,
  Calendar,
  Layers,
  ChevronRight
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
    holiday: 'bg-rose-50 text-rose-600 border-rose-100',
    meeting: 'bg-amber-50 text-amber-600 border-amber-100',
    event: 'bg-indigo-50 text-indigo-600 border-indigo-100'
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
    <div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm ring-1 ring-slate-200/20 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl"></div>
        <div class="space-y-2 relative z-10">
          <div class="flex items-center gap-3">
             <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <CalendarDays :size="24" />
             </div>
             <h2 class="text-3xl font-black text-slate-900 tracking-tight">Agenda Escolar</h2>
          </div>
          <p class="text-slate-500 font-medium ml-15 max-w-xl italic">Gerencie os compromissos institucionais e mantenha a comunidade escolar sempre atualizada.</p>
        </div>
        
        <button @click="showModal = true" class="flex items-center justify-center gap-2 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-xl shadow-indigo-100 active:scale-95 group relative z-10">
          <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" />
          Registrar Novo Evento
        </button>
      </div>

      <!-- Events List -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Sidebar: Event Stats -->
        <div class="space-y-6">
           <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-56 group overflow-hidden relative">
               <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-700"></div>
               <div>
                  <h4 class="text-xs font-black uppercase tracking-widest opacity-60 mb-2">Total na Agenda</h4>
                  <p class="text-5xl font-black">{{ events.length }}</p>
               </div>
               <div class="bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl text-xs font-bold w-fit">
                   Ano Letivo 2026/2027
               </div>
           </div>

           <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
              <h5 class="text-xs font-black text-slate-400 uppercase tracking-widest px-2">Legenda de Tipos</h5>
              <div class="space-y-2">
                 <div v-for="(val, key) in { holiday: 'Feriado/Recesso', meeting: 'Reunião/Pedagógico', event: 'Evento/Atividade' }" :key="key" class="flex items-center gap-3 p-3 rounded-2xl border border-slate-50">
                    <div :class="`w-3 h-3 rounded-full ${getEventBadge(key).split(' ')[0]}`"></div>
                    <span class="text-xs font-bold text-slate-600">{{ val }}</span>
                 </div>
              </div>
           </div>
        </div>

        <!-- Main List -->
        <div class="lg:col-span-2 space-y-4">
          <div v-if="events.length === 0" class="bg-white rounded-3xl p-20 border border-dashed border-slate-200 flex flex-col items-center justify-center text-center space-y-4">
              <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-2">
                 <Calendar :size="40" />
              </div>
              <div>
                 <h4 class="text-xl font-bold text-slate-500">Nenhum evento registrado</h4>
                 <p class="text-slate-400 text-sm font-medium">Os eventos que você adicionar aparecerão aqui nesta linha do tempo.</p>
              </div>
          </div>

          <div v-for="event in events" :key="event.id" class="bg-white rounded-3xl p-6 border border-slate-100 hover:border-indigo-200 transition-all shadow-sm ring-1 ring-slate-200/20 flex items-center justify-between group overflow-hidden relative">
            <div class="flex items-center gap-6">
                <!-- Date Chip -->
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex flex-col items-center justify-center border border-slate-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-500 transition-all shadow-inner">
                   <span class="text-[10px] font-black uppercase leading-none opacity-60">{{ new Date(event.event_date).toLocaleDateString('pt-BR', {month: 'short'}).replace('.', '') }}</span>
                   <span class="text-xl font-black leading-none mt-1">{{ new Date(event.event_date).getDate() }}</span>
                </div>

                <!-- Info -->
                <div class="space-y-1">
                   <div class="flex items-center gap-2">
                      <span :class="`px-2 py-0.5 rounded-lg text-[10px] font-black uppercase border tracking-widest ${getEventBadge(event.type)}`">{{ getEventLabel(event.type) }}</span>
                      <h3 class="text-lg font-bold text-slate-800 tracking-tight leading-none">{{ event.title }}</h3>
                   </div>
                   <p class="text-sm font-medium text-slate-500 italic">{{ event.description || 'Sem descrição adicional.' }}</p>
                </div>
            </div>

            <button @click="deleteEvent(event.id)" class="p-3 bg-rose-50 text-rose-300 hover:text-rose-600 hover:bg-rose-100 rounded-2xl transition-all opacity-0 group-hover:opacity-100 translate-x-4 group-hover:translate-x-0">
               <Trash2 :size="20" />
            </button>
          </div>
        </div>
      </div>

      <!-- Add Event Modal (Ultra Modern) -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md animate-in fade-in duration-300">
         <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl p-10 space-y-8 animate-in zoom-in-95 duration-300 relative border border-white/50">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                  <h3 class="text-2xl font-black text-slate-900 leading-tight">Novo Evento</h3>
                  <p class="text-slate-400 text-sm font-medium italic">Registre uma data importante na agenda.</p>
                </div>
                <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
                   <Plus :size="28" class="rotate-45" />
                </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
               <div class="space-y-1.5">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Título do Evento</label>
                  <input v-model="form.title" type="text" placeholder="Ex: Conselho de Classe" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-slate-800 font-bold placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner" required />
               </div>

               <div class="grid grid-cols-2 gap-5">
                  <div class="space-y-1.5">
                     <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Data</label>
                     <input v-model="form.event_date" type="date" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-slate-800 font-bold focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner" required />
                  </div>
                  <div class="space-y-1.5">
                     <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Categoria</label>
                     <select v-model="form.type" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-slate-800 font-bold focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner appearance-none">
                        <option value="event">Evento Comum</option>
                        <option value="holiday">Feriado/Recesso</option>
                        <option value="meeting">Reunião Docente</option>
                     </select>
                  </div>
               </div>

               <div class="space-y-1.5">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Descrição Breve</label>
                  <textarea v-model="form.description" rows="3" placeholder="Detalhes adicionais sobre o evento..." class="w-full bg-slate-50 border-none rounded-2xl py-4 px-6 text-slate-800 font-bold placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-600 transition-all shadow-inner resize-none"></textarea>
               </div>

               <button type="submit" :disabled="form.processing" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-3xl font-black text-lg shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-3 mt-4 disabled:opacity-50">
                  <span v-if="form.processing">Processando...</span>
                  <span v-else class="flex items-center gap-2">Salvar na Agenda <CheckCircle2 :size="20" /></span>
               </button>
            </form>
         </div>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
