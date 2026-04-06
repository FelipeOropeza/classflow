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
  Users,
  CheckCircle2,
  ArrowRightCircle,
  Layers
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
  status: string;
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
const isEnrollModalOpen = ref(false);
const selectedStudent = ref<Student | null>(null);
const searchQuery = ref('');

const form = useForm({
  name: '',
  birth_date: '',
  registration_number: '',
  guardian_id: '',
});

const enrollForm = useForm({
  class_id: '',
});

const openModal = () => isModalOpen.value = true;
const closeModal = () => {
  isModalOpen.value = false;
  form.reset();
};

const openEnrollModal = (student: Student) => {
  selectedStudent.value = student;
  isEnrollModalOpen.value = true;
};

const closeEnrollModal = () => {
  isEnrollModalOpen.value = false;
  selectedStudent.value = null;
  enrollForm.reset();
};

const submit = () => {
  form.post(route('students.store'), {
    onSuccess: () => closeModal(),
  });
};

const submitEnroll = () => {
  if (!selectedStudent.value) return;
  enrollForm.post(route('students.enroll', selectedStudent.value.id), {
    onSuccess: () => closeEnrollModal(),
  });
};

const deleteStudent = (id: number) => {
  if (confirm('Tem certeza que deseja excluir este aluno?')) {
    useForm({}).delete(route('students.destroy', id));
  }
};

const filteredStudents = computed(() => {
  return props.students.filter(student => 
    student.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    student.registration_number.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatDate = (date: string) => new Date(date).toLocaleDateString('pt-BR');
</script>

<template>
  <Head title="Gerenciar Alunos" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Alunos</h1>
          <p class="text-slate-500 font-medium mt-1 uppercase text-[10px] tracking-widest font-black">Diretório Geral de Estudantes</p>
        </div>
        <button 
          @click="openModal"
          class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-slate-900 text-white px-6 py-3 rounded-2xl font-black shadow-lg shadow-indigo-100 transition-all active:scale-95 text-xs uppercase tracking-widest"
        >
          <Plus :size="16" /> Novo Aluno
        </button>
      </div>

      <!-- Stats Bar -->
      <div class="flex items-center gap-6 mb-8 overflow-x-auto pb-2 custom-scrollbar">
         <div class="bg-white px-6 py-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold">
               {{ students.length }}
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Geral</span>
         </div>
         <div class="bg-white px-6 py-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold">
               {{ students.filter(s => s.enrollments.length > 0).length }}
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Enturmados</span>
         </div>
         <div class="bg-white px-6 py-4 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 shrink-0">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 font-bold animate-pulse">
               {{ students.filter(s => s.enrollments.length === 0).length }}
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aguardando Turma</span>
         </div>
      </div>

      <!-- Search Section -->
      <div class="bg-white p-4 rounded-[28px] border border-slate-100 shadow-sm flex items-center gap-4 mb-8">
          <div class="flex-1 relative group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-colors" :size="20" />
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Buscar aluno por nome ou matrícula..."
              class="w-full bg-slate-50 border-none rounded-2xl py-3.5 pl-12 pr-4 text-xs font-bold text-slate-900 outline-none focus:ring-4 focus:ring-indigo-600/5 transition-all"
            >
          </div>
      </div>

      <!-- Students Table -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50">
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identificação do Aluno</th>
              <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Nº Matrícula</th>
              <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status de Turma</th>
              <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Responsável Legal</th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="student in filteredStudents" :key="student.id" class="group hover:bg-slate-50/20 transition-colors">
              <td class="px-8 py-6">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-xs shadow-sm group-hover:scale-110 transition-transform">
                    {{ student.name[0] }}
                  </div>
                  <div>
                    <p class="font-bold text-slate-900 text-sm leading-none mb-1">{{ student.name }}</p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ formatDate(student.birth_date) }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-6 text-center">
                <span class="text-xs font-black text-slate-600 font-mono tracking-tighter">#{{ student.registration_number }}</span>
              </td>
              <td class="px-6 py-6 text-center">
                <div v-if="student.enrollments.length > 0 && student.enrollments[0].school_class" class="flex flex-col items-center">
                  <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100 uppercase tracking-widest">
                     {{ student.enrollments[0].school_class.name }}
                  </span>
                </div>
                <div v-else class="flex flex-col items-center">
                   <span class="text-[10px] font-black text-rose-500 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100 uppercase tracking-widest">Não Enturmado</span>
                </div>
              </td>
              <td class="px-6 py-6 text-center">
                 <p class="text-xs font-bold text-slate-600">{{ student.guardian.name }}</p>
              </td>
              <td class="px-8 py-6 text-right">
                <div class="flex items-center justify-end gap-3">
                  <button 
                    v-if="student.enrollments.length === 0 || !student.enrollments[0].school_class"
                    @click="openEnrollModal(student)"
                    class="flex items-center gap-2 px-3 py-1.5 bg-indigo-600 hover:bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95"
                  >
                     <ArrowRightCircle :size="14" /> Enturmar
                  </button>
                  <button 
                    @click="deleteStudent(student.id)"
                    class="p-2 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all"
                  >
                    <Trash2 :size="18" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Modal (Novo Aluno) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm animate-in fade-in">
      <div class="bg-white w-full max-w-xl rounded-[32px] p-10 shadow-2xl space-y-10 animate-in zoom-in-95">
          <div class="flex items-center justify-between">
              <h3 class="text-2xl font-black text-slate-900 tracking-tight">Registro de Aluno</h3>
              <button @click="closeModal" class="p-2 bg-slate-50 rounded-xl text-slate-400 hover:text-slate-900 transition-colors">
                  <X :size="20" />
              </button>
          </div>

          <form @submit.prevent="submit" class="space-y-6">
              <div class="space-y-4">
                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nome Completo</label>
                    <input v-model="form.name" type="text" required placeholder="Ex: Lucas Gabriel" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-4 focus:ring-indigo-600/5 outline-none">
                  </div>
                  
                  <div class="grid grid-cols-2 gap-6">
                      <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nascimento</label>
                        <input v-model="form.birth_date" type="date" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-4 focus:ring-indigo-600/5 outline-none">
                      </div>
                      <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Matrícula</label>
                        <input v-model="form.registration_number" type="text" required placeholder="2026001" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-4 focus:ring-indigo-600/5 outline-none">
                      </div>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Responsável Legal</label>
                    <select v-model="form.guardian_id" required class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-4 focus:ring-indigo-600/5 outline-none appearance-none">
                        <option value="" disabled selected>Selecione um responsável...</option>
                        <option v-for="g in guardians" :key="g.id" :value="g.id">{{ g.name }}</option>
                    </select>
                  </div>
              </div>

              <div class="pt-6">
                <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-slate-900 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-indigo-100 flex items-center justify-center gap-3 active:scale-95">
                    <Plus :size="20" /> Finalizar Cadastro
                </button>
              </div>
          </form>
      </div>
    </div>

    <!-- Enroll Modal (Enturmar) -->
    <div v-if="isEnrollModalOpen && selectedStudent" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-md animate-in fade-in">
       <div class="bg-white w-full max-w-lg rounded-[36px] overflow-hidden shadow-2xl animate-in slide-in-from-bottom-8">
          <div class="p-10 space-y-8">
             <div class="flex items-center justify-between">
                <div>
                   <h3 class="text-2xl font-black text-slate-900 tracking-tight">Finalizar Matrícula</h3>
                   <p class="text-xs font-bold text-slate-400 mt-1">Vinculando: <span class="text-indigo-600">{{ selectedStudent.name }}</span></p>
                </div>
                <button @click="closeEnrollModal" class="p-2 bg-slate-50 rounded-xl text-slate-400 hover:text-slate-900 transition-colors">
                   <X :size="20" />
                </button>
             </div>

             <form @submit.prevent="submitEnroll" class="space-y-8 text-center px-4">
                <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl mx-auto flex items-center justify-center shadow-inner">
                   <Layers :size="40" />
                </div>
                
                <div class="space-y-4">
                   <p class="text-sm font-bold text-slate-500">Selecione a turma disponível para este ano letivo:</p>
                   <select v-model="enrollForm.class_id" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-xs font-black text-center focus:border-indigo-600 transition-all outline-none">
                      <option value="" disabled selected>ESCOLHA UMA TURMA</option>
                      <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                   </select>
                </div>

                <div class="flex gap-4">
                   <button type="button" @click="closeEnrollModal" class="flex-1 px-8 py-4 rounded-2xl font-black text-slate-400 hover:bg-slate-50 transition-all uppercase text-[10px] tracking-widest">Desistir</button>
                   <button type="submit" :disabled="enrollForm.processing" class="flex-[2] bg-slate-900 hover:bg-indigo-600 text-white font-black px-8 py-4 rounded-2xl shadow-xl transition-all active:scale-95 uppercase text-[10px] tracking-widest flex items-center justify-center gap-2">
                       <CheckCircle2 :size="16" /> Oficializar Vínculo
                   </button>
                </div>
             </form>
          </div>
       </div>
    </div>

  </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
</style>
