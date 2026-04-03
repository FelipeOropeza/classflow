<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  ShieldCheck, 
  Plus, 
  Search, 
  X,
  Trash2,
  BookOpen,
  Users,
  Layers,
  ArrowRight
} from 'lucide-vue-next';

const props = defineProps<{
  links: any[];
  teachers: any[];
  classes: any[];
  subjects: any[];
}>();

const isModalOpen = ref(false);
const searchQuery = ref('');

const form = useForm({
  teacher_id: '',
  class_id: '',
  subject_id: '',
});

const openModal = () => isModalOpen.value = true;
const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const submit = () => {
  form.post(route('academic-links.store'), {
    onSuccess: () => closeModal(),
  });
};

const deleteLink = (id: number) => {
  if (confirm('Deseja remover este vínculo (Professor-Disciplina-Turma)?')) {
    form.delete(route('academic-links.destroy', id));
  }
};
</script>

<template>
  <Head title="Vínculos Acadêmicos" />

  <AuthenticatedLayout>
    <div class="p-8 max-w-7xl mx-auto space-y-8">
      <!-- Header Section -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[32px] shadow-sm border border-slate-100">
        <div class="flex items-center gap-5">
          <div class="w-16 h-16 rounded-[24px] bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
            <ShieldCheck :size="32" />
          </div>
          <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Vínculos Acadêmicos</h1>
            <p class="text-slate-500 font-bold flex items-center gap-2">
               Atribuição de professores a disciplinas e turmas
            </p>
          </div>
        </div>
        
        <button 
          @click="openModal"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-indigo-100 transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group"
        >
          <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" />
          <span>Vincular Aula</span>
        </button>
      </div>

      <!-- Links Grid (Vínculos) -->
      <div v-if="links.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="link in links" :key="link.id" class="group bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 transition-all duration-300 relative overflow-hidden">
          <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-all">
            <button @click="deleteLink(link.id)" class="bg-rose-50 text-rose-500 p-2 rounded-xl hover:bg-rose-100 transition-colors">
              <Trash2 :size="18" />
            </button>
          </div>

          <div class="space-y-6">
            <!-- Class Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-wider">
              <Layers :size="12" />
              {{ link.school_class.name }}
            </div>

            <!-- Subject & Teacher -->
            <div>
              <h3 class="text-xl font-black text-slate-900 mb-4 group-hover:text-indigo-600 transition-colors">
                {{ link.subject.name }}
              </h3>
              
              <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-white border-2 border-white shadow-sm flex items-center justify-center text-indigo-600 font-bold uppercase transition-transform group-hover:scale-110">
                  {{ link.teacher.name.charAt(0) }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1 text-ellipsis overflow-hidden">Professor</p>
                  <p class="text-sm font-bold text-slate-700 truncate overflow-hidden">{{ link.teacher.name }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Accent -->
          <div class="absolute bottom-0 left-0 w-full h-1.5 bg-slate-50 group-hover:bg-indigo-500 transition-colors"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="links.length === 0" class="bg-white p-20 rounded-[32px] border border-slate-100 text-center shadow-sm">
        <div class="flex flex-col items-center gap-6">
          <div class="w-24 h-24 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
            <ShieldCheck :size="48" />
          </div>
          <div class="max-w-xs mx-auto">
            <h3 class="text-xl font-bold text-slate-900 mb-2">Grade curricular vazia</h3>
            <p class="text-slate-400 font-medium">Vincule professores às suas respectivas turmas e disciplinas para iniciar o período letivo.</p>
          </div>
          <button @click="openModal" class="text-indigo-600 font-black text-sm flex items-center gap-2 hover:gap-3 transition-all">
             Começar agora <ArrowRight :size="16" />
          </button>
        </div>
      </div>

      <!-- Academic Link Modal -->
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
                  <h3 class="text-xl font-black text-slate-900 leading-tight">Novo Vínculo</h3>
                  <p class="text-sm font-medium text-slate-500">Atribuir aula a professor.</p>
                </div>
              </div>
              <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-900 transition-colors">
                <X :size="20" />
              </button>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
              <!-- Class Selection -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Turma</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Layers :size="18" />
                  </div>
                  <select 
                    v-model="form.class_id"
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all text-ellipsis"
                  >
                    <option value="" disabled selected>Selecione a turma...</option>
                    <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>
              </div>

              <!-- Subject Selection -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Disciplina</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <BookOpen :size="18" />
                  </div>
                  <select 
                    v-model="form.subject_id"
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all text-ellipsis"
                  >
                    <option value="" disabled selected>Selecione a disciplina...</option>
                    <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                  </select>
                </div>
              </div>

              <!-- Teacher Selection -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Professor Docente</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Users :size="18" />
                  </div>
                  <select 
                    v-model="form.teacher_id"
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all text-ellipsis"
                  >
                    <option value="" disabled selected>Selecione o professor...</option>
                    <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                  </select>
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
                  <span>{{ form.processing ? 'Vinculando...' : 'Efetivar' }}</span>
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
