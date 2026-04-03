<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { 
  User, 
  Plus, 
  Search, 
  Trash2, 
  X,
  AlertCircle,
  GraduationCap,
  Calendar,
  Hash,
  ShieldCheck,
  MoreVertical,
  Users
} from 'lucide-vue-next';

interface SchoolClass {
  id: number;
  name: string;
}

interface Guardian {
  id: number;
  name: string;
}

interface Enrollment {
  id: number;
  school_class: SchoolClass;
}

interface Student {
  id: number;
  name: string;
  registration_number: string;
  birth_date: string;
  guardian: Guardian;
  enrollments: Enrollment[];
}

const props = defineProps<{
  students: Student[];
  classes: SchoolClass[];
  guardians: Guardian[];
}>();

const isModalOpen = ref(false);
const searchQuery = ref('');

const form = useForm({
  name: '',
  birth_date: '',
  registration_number: '',
  guardian_id: '',
  class_id: '',
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
  form.post(route('students.store'), {
    onSuccess: () => closeModal(),
  });
};

const deleteStudent = (id: number) => {
  if (confirm('Tem certeza que deseja excluir este aluno? Esta ação não pode ser desfeita.')) {
    useForm({}).delete(route('students.destroy', id));
  }
};

const filteredStudents = computed(() => {
  return props.students.filter(student => 
    student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    student.registration_number.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR');
};
</script>

<template>
  <Head title="Gerenciar Alunos" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Alunos</h1>
          <p class="text-slate-500 font-medium mt-1">Gerencie as matrículas e dados dos estudantes.</p>
        </div>
        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-200 transition-all active:scale-95"
        >
          <Plus :size="20" />
          Novo Aluno
        </button>
      </div>

      <!-- Stats & Filter Bar -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
            <GraduationCap :size="24" />
          </div>
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total de Alunos</p>
            <p class="text-2xl font-black text-slate-900">{{ students.length }}</p>
          </div>
        </div>
        
        <div class="md:col-span-3 bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
          <div class="flex-1 relative group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors" :size="20" />
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Buscar aluno por nome ou matrícula..."
              class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-semibold text-slate-900 outline-none focus:border-indigo-600/10 focus:ring-4 focus:ring-indigo-600/5 transition-all"
            >
          </div>
        </div>
      </div>

      <!-- Students Table -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50">
              <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Aluno</th>
              <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Matrícula</th>
              <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Turma Atual</th>
              <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Responsável</th>
              <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="student in filteredStudents" :key="student.id" class="group hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                    {{ student.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-sm">{{ student.name }}</p>
                    <p class="text-xs text-slate-500 font-medium">{{ formatDate(student.birth_date) }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-slate-100 text-slate-600 uppercase tracking-tighter">
                  #{{ student.registration_number }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div v-if="student.enrollments.length > 0" class="flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                  <span class="text-xs font-bold text-slate-700">{{ student.enrollments[0].school_class.name }}</span>
                </div>
                <span v-else class="text-xs font-bold text-rose-500 italic">Não enturmado</span>
              </td>
              <td class="px-6 py-4">
                <p class="text-xs font-bold text-slate-700">{{ student.guardian.name }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button 
                    @click="deleteStudent(student.id)"
                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
                    title="Excluir Aluno"
                  >
                    <Trash2 :size="18" />
                  </button>
                  <button class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                    <MoreVertical :size="18" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td colspan="5" class="px-6 py-20 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                  <Users :size="32" />
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-1">Nenhum aluno encontrado</h3>
                <p class="text-sm font-medium text-slate-500">Tente ajustar sua busca ou cadastre um novo aluno.</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Modal -->
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
                <h3 class="text-xl font-black text-slate-900 leading-tight">Novo Aluno</h3>
                <p class="text-sm font-medium text-slate-500">Cadastre o estudante.</p>
              </div>
            </div>
            <button @click="closeModal" class="p-2 text-slate-400 hover:text-slate-900 transition-colors">
              <X :size="20" />
            </button>
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <!-- Name -->
            <div class="group">
              <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Nome Completo</label>
              <div class="relative">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                  <User :size="18" />
                </div>
                <input 
                  v-model="form.name"
                  type="text" 
                  required
                  placeholder="Nome do aluno"
                  class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                >
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Birth Date -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Nascimento</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Calendar :size="18" />
                  </div>
                  <input 
                    v-model="form.birth_date"
                    type="date" 
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                </div>
              </div>

              <!-- Registration -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Matrícula</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <Hash :size="18" />
                  </div>
                  <input 
                    v-model="form.registration_number"
                    type="text" 
                    required
                    placeholder="2026101"
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                </div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <!-- Class Selection -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Turma</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <GraduationCap :size="18" />
                  </div>
                  <select 
                    v-model="form.class_id"
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                    <option value="" disabled selected>Selecione...</option>
                    <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>
              </div>

              <!-- Guardian Selection -->
              <div class="group">
                <label class="block text-[11px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-600 transition-colors ml-1 mb-1.5">Responsável</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors pointer-events-none">
                    <ShieldCheck :size="18" />
                  </div>
                  <select 
                    v-model="form.guardian_id"
                    required
                    class="w-full bg-slate-50 border-2 border-slate-50 rounded-2xl py-3 pl-12 pr-4 text-sm font-bold text-slate-900 outline-none focus:border-indigo-600/20 focus:bg-white focus:ring-4 focus:ring-indigo-600/5 transition-all"
                  >
                    <option value="" disabled selected>Selecione...</option>
                    <option v-for="g in guardians" :key="g.id" :value="g.id">{{ g.name }}</option>
                  </select>
                </div>
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
                <span>{{ form.processing ? 'Matriculando...' : 'Matricular' }}</span>
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
