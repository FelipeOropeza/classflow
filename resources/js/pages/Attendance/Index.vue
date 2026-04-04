<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
  ClipboardCheck, 
  Search, 
  GraduationCap, 
  User, 
  Save, 
  ChevronRight, 
  BookOpen, 
  CalendarDays,
  Lock,
  Unlock,
  AlertTriangle
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Link {
  id: number;
  school_class: { name: string };
  subject: { name: string };
  teacher?: { name: string };
}

interface Enrollment {
  id: number;
  student: { name: string; registration_number: string };
}

const props = defineProps<{
  links: Link[];
  selectedLink: Link | null;
  date: string;
  enrollments: Enrollment[];
  existingAttendance: Record<number, any>;
  isLocked: boolean;
  canEdit: boolean;
}>();

const selectedLinkIdLocal = ref(props.selectedLink?.id || '');
const selectedDate = ref(props.date || new Date().toISOString().split('T')[0]);

const form = useForm({
  link_id: props.selectedLink?.id || '',
  date: props.date || '',
  attendance: [] as { enrollment_id: number; status: string; observation: string }[]
});

// Inicializar formulário quando houver matrículas
watch(() => props.enrollments, (newEnrollments) => {
  if (newEnrollments.length > 0) {
    form.attendance = newEnrollments.map(enrollment => {
      const existing = props.existingAttendance[enrollment.id];
      return {
        enrollment_id: enrollment.id,
        status: existing ? existing.status : 'present',
        observation: existing ? existing.observation : ''
      };
    });
  } else {
    form.attendance = [];
  }
}, { immediate: true });

const changeFilters = () => {
  router.get(route('attendance.index'), { link_id: selectedLinkIdLocal.value, date: selectedDate.value }, {
    preserveState: true,
    replace: true,
  });
  form.link_id = selectedLinkIdLocal.value;
  form.date = selectedDate.value;
};

const submit = () => {
  if (!props.canEdit) return;
  form.post(route('attendance.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="Controle de Frequência" />

  <AuthenticatedLayout>
    <template #default>
      <div class="space-y-8 animate-in fade-in duration-700">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <div>
            <h2 class="text-3xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
              <div class="p-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100">
                <ClipboardCheck class="text-white" :size="24" />
              </div>
              Controle de Frequência
            </h2>
            <p class="text-slate-500 mt-2 font-medium">Realize a chamada diária e envie os registros para a diretoria.</p>
          </div>
          
          <div v-if="isLocked" class="flex items-center gap-2 px-4 py-2 bg-rose-50 rounded-2xl text-rose-600 font-bold border border-rose-100 animate-pulse transition-all">
             <Lock :size="18" />
             Chamada Trancada (Enviada)
          </div>
          <div v-else-if="!selectedLink" class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-2xl text-slate-400 font-bold border border-slate-100">
             <Unlock :size="18" />
             Aguardando Seleção
          </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white rounded-3xl p-8 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1">Turma & Disciplina</label>
            <div class="relative">
              <select 
                v-model="selectedLinkIdLocal"
                @change="changeFilters"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer appearance-none"
              >
                <option value="">Selecione um vínculo...</option>
                <option v-for="link in links" :key="link.id" :value="link.id">
                  {{ link.school_class.name }} - {{ link.subject.name }}
                </option>
              </select>
              <BookOpen class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>

          <div class="space-y-3">
            <label class="text-sm font-bold text-slate-700 ml-1">Data da Chamada</label>
            <div class="relative">
              <input 
                v-model="selectedDate"
                type="date"
                @change="changeFilters"
                class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3.5 pl-11 text-sm font-semibold text-slate-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
              >
              <CalendarDays class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="20" />
            </div>
          </div>
        </div>

        <!-- Disclaimer Data Retroativa/Futura -->
        <div v-if="selectedDate !== new Date().toISOString().split('T')[0] && selectedLink" class="p-6 bg-amber-50 rounded-3xl border border-amber-100 flex items-start gap-4 text-amber-700 animate-in fade-in duration-500">
           <AlertTriangle class="shrink-0 mt-0.5" :size="22" />
           <div>
              <h4 class="font-bold text-lg leading-tight mb-1">Aviso de Restrição Temporal</h4>
              <p class="text-sm font-medium leading-relaxed opacity-90">
                 De acordo com as diretrizes de segurança escolar, a chamada só pode ser realizada para o dia atual. O modo de edição está <b>desabilitado</b> para datas retroativas ou futuras.
              </p>
           </div>
        </div>

        <!-- Attendance List -->
        <div v-if="selectedLink" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden ring-1 ring-slate-200/20 animate-in slide-in-from-bottom duration-500">
          <div class="p-8 border-b border-slate-50 bg-slate-50/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                <GraduationCap :size="24" />
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900">Alunos Matriculados</h3>
                <p class="text-slate-500 text-sm font-medium">Chamada para {{ selectedLink.school_class.name }} | {{ selectedLink.subject.name }}</p>
              </div>
            </div>
            
            <button 
               v-if="canEdit"
               @click="submit"
               :disabled="form.processing"
               class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 disabled:opacity-50 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg shadow-indigo-200 flex items-center gap-2"
            >
               <Save v-if="!form.processing" :size="20" />
               <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
               {{ form.processing ? 'Enviando...' : 'Finalizar & Travar' }}
            </button>
            <div v-else class="flex flex-col items-end">
               <div class="text-rose-500 font-bold flex items-center gap-1.5 px-4 py-2 bg-rose-50 rounded-xl border border-rose-100">
                  <Lock :size="16" />
                  Edição Proibida
               </div>
               <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 tracking-widest">Enviado por {{ selectedLink.teacher?.name }}</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50">
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider">Aluno</th>
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider">Matrícula</th>
                  <th class="px-8 py-5 text-sm font-bold text-slate-500 uppercase tracking-wider w-80">Presença</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(item, index) in form.attendance" :key="item.enrollment_id" class="hover:bg-slate-50/50 transition-colors group">
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-400 transition-colors capitalize font-bold">
                        {{ enrollments[index].student.name.charAt(0) }}
                      </div>
                      <span class="font-bold text-slate-700">{{ enrollments[index].student.name }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-6">
                    <span class="text-sm font-mono text-slate-400 group-hover:text-slate-600 transition-colors">{{ enrollments[index].student.registration_number }}</span>
                  </td>
                  <td class="px-8 py-6">
                    <div class="flex items-center gap-1.5 p-1 bg-slate-50 rounded-2xl w-fit border border-slate-200">
                       <button 
                        v-for="status in [{val: 'present', label: 'Presente'}, {val: 'absent', label: 'Falta'}, {val: 'late', label: 'Atraso'}]"
                        :key="status.val"
                        @click="canEdit ? item.status = status.val : null"
                        :disabled="!canEdit"
                        :class="[
                          'px-4 py-1.5 rounded-xl text-xs font-bold transition-all disabled:cursor-not-allowed',
                          item.status === status.val 
                             ? (status.val === 'present' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : status.val === 'absent' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white')
                             : 'text-slate-400 hover:text-slate-600'
                        ]"
                       >
                         {{ status.label }}
                       </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div v-if="enrollments.length === 0" class="p-16 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
              <User :size="32" />
            </div>
            <h4 class="text-slate-900 font-bold">Nenhum aluno encontrado</h4>
            <p class="text-slate-500 text-sm mt-1">Não existem matrículas ativas para esta turma.</p>
          </div>
        </div>

        <!-- Empty State No Selection -->
        <div v-else class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center text-center space-y-6 shadow-sm border border-slate-100 ring-1 ring-slate-200/20">
          <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-400">
            <Search :size="40" />
          </div>
          <div class="max-w-xs">
            <h2 class="text-xl font-bold text-slate-900">Selecione uma turma</h2>
            <p class="text-slate-500 mt-2">Escolha o vínculo para realizar a chamada do dia.</p>
          </div>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
