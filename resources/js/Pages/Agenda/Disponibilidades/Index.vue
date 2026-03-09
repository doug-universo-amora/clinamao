<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    disponibilidades: [Array, Object],
    profissionais: Array,
    profissionalSelecionado: Number,
});

const profissionalId = ref(props.profissionalSelecionado);
const editandoId = ref(null);

const form = useForm({
    profissional_id: '',
    dia_semana: '',
    hora_inicio: '',
    hora_fim: '',
    duracao_consulta: '',
});

const editForm = useForm({
    profissional_id: '',
    dia_semana: '',
    hora_inicio: '',
    hora_fim: '',
    duracao_consulta: '',
});

function selecionarProfissional() {
    router.get(route('disponibilidades.index'), { profissional_id: profissionalId.value }, { preserveState: true, replace: true });
}

function novaDisponibilidade() {
    form.profissional_id = profissionalId.value;
    form.post(route('disponibilidades.store'), {
        onSuccess: () => form.reset('dia_semana', 'hora_inicio', 'hora_fim', 'duracao_consulta'),
    });
}

function editarDisponibilidade(disp) {
    editandoId.value = disp.id;
    editForm.profissional_id = disp.profissional_id;
    editForm.dia_semana = disp.dia_semana;
    editForm.hora_inicio = disp.hora_inicio?.substring(0, 5);
    editForm.hora_fim = disp.hora_fim?.substring(0, 5);
    editForm.duracao_consulta = disp.duracao_consulta || '';
}

function salvarEdicao(id) {
    editForm.put(route('disponibilidades.update', id), {
        onSuccess: () => { editandoId.value = null; },
    });
}

const confirmState = ref({ show: false, title: '', message: '', action: null });

function excluir(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Disponibilidade',
        message: 'Tem certeza que deseja excluir esta disponibilidade?',
        action: (callbacks) => router.delete(route('disponibilidades.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}

const diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
</script>

<template>
    <Head title="Disponibilidades" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Disponibilidades</h2>
                <Link :href="route('agenda.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar à Agenda</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Seletor de profissional -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex items-center space-x-3">
                        <InputLabel value="Profissional:" />
                        <select v-model="profissionalId" @change="selecionarProfissional" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-64">
                            <option :value="null">Selecione...</option>
                            <option v-for="p in profissionais" :key="p.id" :value="p.id">{{ p.user?.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Formulário de nova disponibilidade -->
                <div v-if="profissionalId" class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Adicionar Disponibilidade</h3>
                    <form @submit.prevent="novaDisponibilidade" class="flex flex-wrap items-end gap-3">
                        <div>
                            <InputLabel value="Dia" class="text-xs" />
                            <select v-model="form.dia_semana" class="rounded-md border-gray-300 text-sm" required>
                                <option value="">Selecione...</option>
                                <option v-for="(nome, idx) in diasSemana" :key="idx" :value="idx">{{ nome }}</option>
                            </select>
                            <InputError :message="form.errors.dia_semana" />
                        </div>
                        <div>
                            <InputLabel value="Início" class="text-xs" />
                            <TextInput type="time" class="text-sm" v-model="form.hora_inicio" required />
                            <InputError :message="form.errors.hora_inicio" />
                        </div>
                        <div>
                            <InputLabel value="Fim" class="text-xs" />
                            <TextInput type="time" class="text-sm" v-model="form.hora_fim" required />
                            <InputError :message="form.errors.hora_fim" />
                        </div>
                        <div>
                            <InputLabel value="Duração (min)" class="text-xs" />
                            <TextInput type="number" class="text-sm w-20" v-model="form.duracao_consulta" placeholder="—" min="5" />
                        </div>
                        <PrimaryButton :disabled="form.processing" class="text-sm">Adicionar</PrimaryButton>
                    </form>
                </div>

                <!-- Tabela de disponibilidades -->
                <div v-if="profissionalId" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Dia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Início</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Fim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Duração</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template v-for="disp in disponibilidades" :key="disp.id">
                                <!-- Modo visualização -->
                                <tr v-if="editandoId !== disp.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ diasSemana[disp.dia_semana] }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ disp.hora_inicio?.substring(0,5) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ disp.hora_fim?.substring(0,5) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ disp.duracao_consulta ? disp.duracao_consulta + ' min' : 'Padrão' }}</td>
                                    <td class="px-6 py-3 text-right text-sm">
                                        <button @click="editarDisponibilidade(disp)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                        <button @click="excluir(disp.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </td>
                                </tr>
                                <!-- Modo edição inline -->
                                <tr v-else class="bg-yellow-50">
                                    <td class="px-6 py-3">
                                        <select v-model="editForm.dia_semana" class="rounded-md border-gray-300 text-sm">
                                            <option v-for="(nome, idx) in diasSemana" :key="idx" :value="idx">{{ nome }}</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-3"><TextInput type="time" class="text-sm" v-model="editForm.hora_inicio" /></td>
                                    <td class="px-6 py-3"><TextInput type="time" class="text-sm" v-model="editForm.hora_fim" /></td>
                                    <td class="px-6 py-3"><TextInput type="number" class="text-sm w-20" v-model="editForm.duracao_consulta" /></td>
                                    <td class="px-6 py-3 text-right text-sm">
                                        <button @click="salvarEdicao(disp.id)" class="text-green-600 hover:text-green-900 mr-2">Salvar</button>
                                        <button @click="editandoId = null" class="text-gray-600 hover:text-gray-900">Cancelar</button>
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="!disponibilidades?.length">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Nenhuma disponibilidade cadastrada.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="confirmState.show"
            :title="confirmState.title"
            :message="confirmState.message"
            @close="confirmState.show = false"
            @confirm="confirmState.action"
        />
    </AuthenticatedLayout>
</template>
