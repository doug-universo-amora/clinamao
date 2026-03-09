<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    bloqueios: Array,
    profissionais: Array,
});

const showForm = ref(false);

const form = useForm({
    profissional_id: props.profissionais?.length === 1 ? props.profissionais[0].id : '',
    data_inicio: '',
    data_fim: '',
    motivo: '',
});

function submit() {
    form.post(route('bloqueios.store'), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
}

const confirmState = ref({ show: false, title: '', message: '', action: null });

function excluir(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Bloqueio',
        message: 'Tem certeza que deseja excluir este bloqueio?',
        action: (callbacks) => router.delete(route('bloqueios.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}

function formatarDataHora(str) {
    if (!str) return '—';
    const d = new Date(str);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <Head title="Bloqueios de Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Bloqueios de Agenda</h2>
                <div class="flex space-x-2">
                    <button @click="showForm = !showForm" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        {{ showForm ? 'Fechar' : '+ Novo Bloqueio' }}
                    </button>
                    <Link :href="route('agenda.index')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        ← Agenda
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <!-- Error Flash -->
                <div v-if="$page.props.flash?.error" class="mb-4 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <!-- Formulário de novo bloqueio -->
                <div v-if="showForm" class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Novo Bloqueio</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="profissional_id" value="Profissional *" />
                                <select id="profissional_id" v-model="form.profissional_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500" :disabled="profissionais.length === 1" required>
                                    <option value="" v-if="profissionais.length > 1">Selecione...</option>
                                    <option v-for="p in profissionais" :key="p.id" :value="p.id">{{ p.user?.name }}</option>
                                </select>
                                <InputError :message="form.errors.profissional_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="motivo" value="Motivo" />
                                <TextInput id="motivo" type="text" class="mt-1 block w-full" v-model="form.motivo" placeholder="Ex: Férias, Congresso..." />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="data_inicio" value="Início *" />
                                <TextInput id="data_inicio" type="datetime-local" class="mt-1 block w-full" v-model="form.data_inicio" required />
                                <InputError :message="form.errors.data_inicio" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="data_fim" value="Fim *" />
                                <TextInput id="data_fim" type="datetime-local" class="mt-1 block w-full" v-model="form.data_fim" required />
                                <InputError :message="form.errors.data_fim" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <PrimaryButton :disabled="form.processing">Salvar Bloqueio</PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Lista de bloqueios -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Profissional</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Início</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Fim</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Motivo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="bloqueio in bloqueios" :key="bloqueio.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ bloqueio.profissional?.user?.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatarDataHora(bloqueio.data_inicio) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ formatarDataHora(bloqueio.data_fim) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ bloqueio.motivo || '—' }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <button @click="excluir(bloqueio.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                </td>
                            </tr>
                            <tr v-if="!bloqueios?.length">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum bloqueio cadastrado.</td>
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
