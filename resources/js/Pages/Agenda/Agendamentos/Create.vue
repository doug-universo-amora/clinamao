<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    profissionais: Array,
    pacientes: Array,
    salas: Array,
    dataInicial: String,
    profissionalInicial: [String, Number],
    horaInicial: String,
});

const form = useForm({
    profissional_id: props.profissionalInicial || '',
    paciente_id: '',
    sala_id: '',
    data_consulta: props.dataInicial || new Date().toISOString().split('T')[0],
    hora_inicio: props.horaInicial || '',
    hora_fim: '',
    observacoes: '',
});

const horariosDisponiveis = ref([]);
const carregandoHorarios = ref(false);
const pacienteSearch = ref('');

// Filtrar pacientes por busca
const pacientesFiltrados = ref(props.pacientes);
watch(pacienteSearch, (val) => {
    if (!val) {
        pacientesFiltrados.value = props.pacientes;
    } else {
        const term = val.toLowerCase();
        pacientesFiltrados.value = props.pacientes.filter(p =>
            p.nome.toLowerCase().includes(term) || (p.cpf && p.cpf.includes(term))
        );
    }
});

// Buscar horários quando profissional e data são selecionados
watch([() => form.profissional_id, () => form.data_consulta], async ([profId, dt]) => {
    if (profId && dt) {
        carregandoHorarios.value = true;
        try {
            const response = await fetch(route('api.horarios', { profissional_id: profId, data: dt }));
            horariosDisponiveis.value = await response.json();
        } catch (e) {
            horariosDisponiveis.value = [];
        }
        carregandoHorarios.value = false;
    } else {
        horariosDisponiveis.value = [];
    }
}, { immediate: true });

function selecionarHorario(slot) {
    if (!slot.disponivel) return;
    form.hora_inicio = slot.hora_inicio;
    form.hora_fim = slot.hora_fim;
}

function submit() {
    form.transform((data) => ({
        ...data,
        data: data.data_consulta
    })).post(route('agendamentos.store'));
}

const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
</script>

<template>
    <Head title="Novo Agendamento" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Agendamento</h2>
                <Link :href="route('agenda.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar à Agenda</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Formulário -->
                    <div class="lg:col-span-2 overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <InputLabel for="profissional_id" value="Profissional *" />
                                    <select id="profissional_id" v-model="form.profissional_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="">Selecione...</option>
                                        <option v-for="p in profissionais" :key="p.id" :value="p.id">{{ p.user?.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.profissional_id" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="data_consulta" value="Data *" />
                                    <TextInput id="data_consulta" type="date" class="mt-1 block w-full" v-model="form.data_consulta" required />
                                    <InputError :message="form.errors.data" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="paciente_id" value="Paciente *" />
                                <TextInput
                                    type="text"
                                    class="mb-2 block w-full"
                                    placeholder="Buscar paciente..."
                                    v-model="pacienteSearch"
                                />
                                <select id="paciente_id" v-model="form.paciente_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required size="4">
                                    <option v-for="p in pacientesFiltrados" :key="p.id" :value="p.id">{{ p.nome }} {{ p.cpf ? `(${p.cpf})` : '' }}</option>
                                </select>
                                <InputError :message="form.errors.paciente_id" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div>
                                    <InputLabel for="hora_inicio" value="Horário Início *" />
                                    <TextInput id="hora_inicio" type="time" class="mt-1 block w-full" v-model="form.hora_inicio" required />
                                    <InputError :message="form.errors.hora_inicio" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="hora_fim" value="Horário Fim" />
                                    <TextInput id="hora_fim" type="time" class="mt-1 block w-full" v-model="form.hora_fim" />
                                    <InputError :message="form.errors.hora_fim" class="mt-2" />
                                    <p class="mt-1 text-xs text-gray-400">Se vazio, será calculado automaticamente.</p>
                                </div>
                                <div>
                                    <InputLabel for="sala_id" value="Sala" />
                                    <select id="sala_id" v-model="form.sala_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Nenhuma</option>
                                        <option v-for="s in salas" :key="s.id" :value="s.id">{{ s.nome }}</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <InputLabel for="observacoes" value="Observações" />
                                <textarea id="observacoes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="2" v-model="form.observacoes"></textarea>
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton :disabled="form.processing">Agendar</PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Painel de Horários Disponíveis -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Horários Disponíveis</h3>

                        <div v-if="carregandoHorarios" class="text-center py-8 text-gray-400 text-sm">Carregando...</div>

                        <div v-else-if="!form.profissional_id || !form.data_consulta" class="text-center py-8 text-gray-400 text-sm">
                            Selecione profissional e data.
                        </div>

                        <div v-else-if="!horariosDisponiveis.length" class="text-center py-8 text-gray-400 text-sm">
                            Sem disponibilidade nesta data.
                        </div>

                        <div v-else class="grid grid-cols-2 gap-2">
                            <button
                                v-for="slot in horariosDisponiveis"
                                :key="slot.hora_inicio"
                                @click="selecionarHorario(slot)"
                                :disabled="!slot.disponivel"
                                :class="[
                                    'rounded-md px-3 py-2 text-sm font-medium transition',
                                    slot.hora_inicio === form.hora_inicio
                                        ? 'bg-indigo-600 text-white ring-2 ring-indigo-300'
                                        : slot.disponivel
                                            ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'
                                            : 'bg-gray-100 text-gray-300 cursor-not-allowed line-through'
                                ]"
                            >
                                {{ slot.hora_inicio }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
