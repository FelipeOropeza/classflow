<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  BookOpen, 
  Plus, 
  Search, 
  MoreVertical, 
  Trash2, 
  Edit2, 
  BookMarked,
  X,
  AlertCircle
} from 'lucide-vue-next';

interface Subject {
  id: number;
  name: string;
  slug: string;
  created_at: string;
}

const props = defineProps<{
  subjects: Subject[];
}>();

const isModalOpen = ref(false);
const searchQuery = ref('');

const form = useForm({
  name: '',
});

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
  form.clearErrors();
};

const submit = () => {
  form.post(route('subjects.store'), {
    onSuccess: () => closeModal(),
  });
};

const deleteSubject = (id: number) => {
  if (confirm('Tem certeza que deseja excluir esta disciplina?')) {
    useForm({}).delete(route('subjects.destroy', id));
  }
};
</script>

<template>
  <Head title="Gerenciar Disciplinas" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Disciplinas</h1>
          <p class="text-slate-500 font-medium mt-1">Gerencie a grade curricular da sua escola.</p>
        </div>
        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all active:scale-95"
        >
          <Plus :size="20" />
          Nova Disciplina
        </button>
      </div>

      <!-- Stats & Filter Bar -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
            <BookMarked :size="24" />
          </div>
          <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total de Disciplinas</p>
            <p class="text-2xl font-black text-slate-900">{{ subjects.length }}</p>
          </div>
        </div>
        
        <div class="md:col-span-2 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="flex-1 relative group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors" :size="20" />
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Buscar por nome da disciplina..."
              class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-semibold text-slate-900 outline-none focus:border-indigo-600/10 focus:ring-4 focus:ring-indigo-600/5 transition-all"
            >
          </div>
        </div>
      </div>

      <!-- Subjects Grid -->
      <div v-if="subjects.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div 
          v-for="subject in subjects" 
          :key="subject.id"
          class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 overflow-hidden"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                <BookOpen :size="24" />
              </div>
              <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button 
                  @click="deleteSubject(subject.id)"
                  class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
                >
                  <Trash2 :size="18" />
                </button>
              </div>
            </div>
            
            <h3 class="text-lg font-black text-slate-900 mb-1 truncate">{{ subject.name }}</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ subject.slug }}</p>
          </div>
          
          <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">ID: CF-{{ String(subject.id).padStart(3, '0') }}</span>
            <Link 
              :href="route('subjects.index')" 
              class="text-xs font-bold text-indigo-600 hover:text-indigo-800"
            >
              Ver Grade
            </Link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-20 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
          <BookMarked :size="40" />
        </div>
        <h3 class="text-2xl font-black text-slate-900 mb-2">Nenhuma disciplina ainda</h3>
        <p class="text-slate-500 font-medium max-w-sm mx-auto mb-8">Comece criando a grade curricular da sua escola para poder vincular às turmas futuramente.</p>
        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all active:scale-95"
        >
          <Plus :size="20" />
          Cadastrar Primeira Disciplina
        </button>
      </div>
    </div>

    <!-- Create Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
      
      <div class="relative bg-white w-full max-w-lg rounded-[32px] shadow-2xl overflow-hidden scale-in">
        <div class="p-8">
          <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                <Plus :size="24" />
              </div>
              <div>
                <h3 class="text-xl font-black text-slate-900">Nova Disciplina</h3>
                <p class="text-sm font-medium text-slate-500">Adicione uma matéria à grade.</p>
              </div>
            </div>
            <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-900 transition-colors">
              <X :size="24" />
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-2 group">
              <label class="text-xs font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors">Nome da Disciplina</label>
              <div class="relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                  <BookOpen :size="20" />
                </div>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  placeholder="Ex: Física Quântica, Artes, Ética..."
                  class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-4 pl-12 pr-4 text-sm font-semibold text-slate-900 outline-none focus:border-indigo-600/10 focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  autofocus
                >
              </div>
              <p v-if="form.errors.name" class="text-xs text-rose-500 font-bold flex items-center gap-1 mt-1">
                <AlertCircle :size="12" /> {{ form.errors.name }}
              </p>
            </div>

            <div class="pt-4 flex gap-3">
              <button 
                type="button"
                @click="closeModal"
                class="flex-1 px-6 py-4 rounded-2xl font-black text-slate-500 hover:bg-slate-50 transition-all active:scale-95"
              >
                Cancelar
              </button>
              <button 
                type="submit"
                :disabled="form.processing"
                class="flex-[2] bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-2xl font-black shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-2"
              >
                <Plus v-if="!form.processing" :size="20" />
                <span>{{ form.processing ? 'Registrando...' : 'Criar Disciplina' }}</span>
              </button>
            </div>
          </form>
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
