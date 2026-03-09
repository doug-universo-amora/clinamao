<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    pacientes: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('pacientes.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

const confirmState = ref({ show: false, title: '', message: '', action: null });

function destroy(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Paciente',
        message: 'Tem certeza que deseja excluir este paciente?',
        action: (callbacks) => router.delete(route('pacientes.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}

function statusLabel(status) {
    const labels = { ativo: 'Ativo', bloqueado: 'Bloqueado', alta: 'Alta' };
    return labels[status] || status;
}

function statusClass(status) {
    const classes = {
        ativo: 'bg-green-100 text-green-800',
        bloqueado: 'bg-red-100 text-red-800',
        alta: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}
</script>

<template>
    <Head title="Pacientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Pacientes</h2>
                <Link
                    :href="route('pacientes.create')"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition"
                >
                    + Novo Paciente
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Search -->
                <div class="mb-4">
                    <TextInput
                        type="text"
                        class="w-full sm:w-96"
                        placeholder="Buscar por nome, CPF ou email..."
                        v-model="search"
                    />
                </div>

                <!-- Mobile Cards -->
                <div class="sm:hidden space-y-3">
                    <div v-for="paciente in pacientes.data" :key="paciente.id" class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-900">{{ paciente.nome }}</span>
                            <span :class="statusClass(paciente.status)" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ statusLabel(paciente.status) }}
                            </span>
                        </div>
                        <p v-if="paciente.cpf" class="text-xs text-gray-500 mb-1">CPF: {{ paciente.cpf }}</p>
                        <p v-if="paciente.telefone" class="text-xs text-gray-500 mb-1">Tel: {{ paciente.telefone }}</p>
                        <p v-if="paciente.email" class="text-xs text-gray-400 mb-2">{{ paciente.email }}</p>
                        <div class="flex justify-end space-x-3">
                            <Link :href="route('pacientes.show', paciente.id)" class="text-sm text-gray-600">Ver</Link>
                            <Link :href="route('pacientes.edit', paciente.id)" class="text-sm text-indigo-600">Editar</Link>
                            <button @click="destroy(paciente.id)" class="text-sm text-red-600">Excluir</button>
                        </div>
                    </div>
                    <div v-if="!pacientes.data?.length" class="bg-white rounded-lg shadow-sm p-8 text-center text-sm text-gray-500">
                        Nenhum paciente encontrado.
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">CPF</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="paciente in pacientes.data" :key="paciente.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ paciente.nome }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ paciente.cpf || '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ paciente.telefone || '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ paciente.email || '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="statusClass(paciente.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                            {{ statusLabel(paciente.status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <Link :href="route('pacientes.show', paciente.id)" class="text-gray-600 hover:text-gray-900 mr-3">Ver</Link>
                                        <Link :href="route('pacientes.edit', paciente.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                        <button @click="destroy(paciente.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </td>
                                </tr>
                                <tr v-if="!pacientes.data?.length">
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum paciente encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pacientes.links?.length > 3" class="mt-4 flex justify-center overflow-x-auto">
                    <nav class="flex items-center space-x-1">
                        <template v-for="link in pacientes.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-2 text-sm rounded-md transition"
                                :class="link.active ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                                v-html="link.label"
                                preserve-state
                            />
                            <span v-else class="px-3 py-2 text-sm text-gray-400" v-html="link.label" />
                        </template>
                    </nav>
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
