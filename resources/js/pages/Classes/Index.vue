<script setup lang="ts">
import { Head, useForm, Link, usePage, router } from '@inertiajs/vue3';
import { 
  Users, 
  Plus, 
  Trash2, 
  Edit, 
  Clock, 
  Eye, 
  Calendar,
  Layers,
  ArrowRight,
  FileDown,
  ShieldCheck,
  ShieldAlert,
  ToggleLeft,
  ToggleRight
} from 'lucide-vue-next';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps<{
  classes: any[];
  activeYear: string;
  academic_year_id: number;
}>();

const page = usePage();
const isAdmin = computed(() => {
    const auth = page.props.auth as any;
    return ['admin', 'director'].includes(auth?.user?.role);
});

const showCreateModal = ref(false);

const form = useForm({
  name: '',
  academic_year_id: props.academic_year_id,
});

const submit = () => {
  form.post(route('classes.store'), {
    onSuccess: () => {
      showCreateModal.value = false;
      form.reset();
    },
  });
};

const deleteClass = (id: number) => {
    if (confirm('Tem certeza que deseja excluir esta turma?')) {
        router.delete(route('classes.destroy', id), {
            preserveScroll: true
        });
    }
};

const toggleActive = (id: number) => {
    router.patch(route('classes.toggle-active', id), {}, {
        preserveScroll: true
    });
};

const alert = (msg: string) => window.alert(msg);
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Gestão de Turmas" />

    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Turmas</h2>
        <p class="mt-1.5 text-slate-500 font-medium tracking-wide">
          Gerencie as turmas ativas do ano letivo {{ activeYear }}.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <a 
          v-if="isAdmin"
          :href="route('classes.schedules.pdf-all')"
          target="_blank"
          class="bg-white hover:bg-slate-50 text-slate-700 px-6 py-2.5 rounded-xl font-bold border border-slate-200 shadow-sm transition-all active:scale-95 text-sm flex items-center gap-2"
        >
          <FileDown :size="18" />
          Exportar Todos (PDF)
        </a>
        <button 
          v-if="isAdmin"
          @click="showCreateModal = true"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-xl shadow-indigo-200 transition-all active:scale-95 text-sm flex items-center gap-2 w-fit"
        >
          <Plus :size="18" />
          Nova Turma
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="classes.length === 0" class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Users :size="32" class="text-slate-300" />
        </div>
        <h3 class="text-lg font-black text-slate-900 mb-1">Nenhuma turma encontrada</h3>
        <p class="text-slate-500 text-sm mb-6">Comece criando sua primeira turma para o ano letivo ativo.</p>
        <button 
            @click="showCreateModal = true"
            class="text-indigo-600 font-bold hover:bg-indigo-50 px-4 py-2 rounded-lg transition-colors border border-indigo-100"
        >
            Clique aqui para adicionar
        </button>
    </div>

    <!-- Classes Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="cls in classes" 
        :key="cls.id"
        class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/30 hover:shadow-2xl hover:shadow-indigo-100 transition-all group p-6"
      >
        <div class="flex items-center justify-between mb-6">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:scale-110 transition-transform">
                <Layers :size="20" />
            </div>
            <div class="flex gap-2">
                <button 
                  v-if="isAdmin"
                  @click="toggleActive(cls.id)" 
                  class="p-2 transition-all rounded-lg"
                  :class="cls.is_active ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:text-indigo-600 hover:bg-indigo-50'"
                  :title="cls.is_active ? 'Desativar Turma' : 'Ativar Turma'"
                >
                    <component :is="cls.is_active ? ToggleRight : ToggleLeft" :size="20" />
                </button>
                <Link 
                    :href="route('classes.schedules.index', cls.id)"
                    class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                    title="Ver Horário"
                >
                    <Clock :size="18" />
                </Link>
                <button @click="alert('Módulo de edição em desenvolvimento.')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Editar Turma">
                    <Edit :size="18" />
                </button>
                <button @click="deleteClass(cls.id)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Excluir Turma">
                    <Trash2 :size="18" />
                </button>
            </div>
        </div>
        
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xl font-black text-slate-900 tracking-tight">{{ cls.name }}</h4>
            <span 
              class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest border"
              :class="cls.is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100'"
            >
              {{ cls.is_active ? 'Ativa' : 'Inativa' }}
            </span>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">
            <Calendar :size="12" />
            {{ activeYear }}
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
            <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Alunos</span>
                <span class="text-lg font-black text-slate-900">{{ cls.enrollments_count }}</span>
            </div>
            <Link :href="route('classes.schedules.index', cls.id)" class="bg-slate-900 text-white p-2 rounded-xl hover:bg-indigo-600 transition-colors">
                <ArrowRight :size="20" />
            </Link>
        </div>
      </div>
    </div>

    <!-- Simple Modal Overlay -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden scale-in animate-[scale-in_0.2s_ease-out]">
            <div class="p-8 border-b border-slate-50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">Nova Turma</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                        <X :size="24" />
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Nome da Turma</label>
                        <input 
                            v-model="form.name"
                            type="text" 
                            placeholder="ex: 1º Ano A"
                            class="w-full bg-slate-50 border-2 border-transparent rounded-2xl px-5 py-4 text-sm font-bold focus:border-indigo-500 focus:bg-white focus:ring-0 transition-all transition-all outline-none"
                            required
                        />
                        <div v-if="form.errors.name" class="text-rose-500 text-xs font-bold mt-2 ml-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-2xl flex items-center gap-3">
                        <Calendar :size="18" class="text-indigo-600" />
                        <div>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Ano Letivo Ativo</p>
                            <p class="text-sm font-bold text-indigo-700">{{ activeYear }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            @click="showCreateModal = false"
                            class="flex-1 px-6 py-4 rounded-2xl text-sm font-black text-slate-500 hover:bg-slate-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="flex-[2] bg-slate-900 hover:bg-indigo-600 text-white px-6 py-4 rounded-2xl text-sm font-black shadow-xl shadow-slate-200 transition-all flex items-center justify-center gap-2"
                        >
                            <Plus v-if="!form.processing" :size="18" />
                            <span v-else class="animate-spin border-2 border-white/20 border-t-white rounded-full w-4 h-4"></span>
                            Criar Turma
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  </AuthenticatedLayout>
</template>

<script lang="ts">
import { X } from 'lucide-vue-next';
export default {
    components: { X }
}
</script>

<style scoped>
@keyframes scale-in {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
