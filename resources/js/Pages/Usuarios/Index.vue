<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { ref } from 'vue';

const props = defineProps({
    usuarios: Array,
});

const confirmState = ref({ show: false, title: '', message: '', action: null });

function destroy(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Usuário',
        message: 'Tem certeza que deseja excluir este usuário?',
        action: (callbacks) => router.delete(route('usuarios.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}
</script>

<template>
    <Head title="Usuários" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Usuários</h2>
                <Link
                    :href="route('usuarios.create')"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition"
                >
                    + Novo Usuário
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Mobile Cards -->
                <div class="sm:hidden space-y-3">
                    <div v-for="usuario in usuarios" :key="usuario.id" class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-900">{{ usuario.name }}</span>
                            <span :class="usuario.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ usuario.status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">{{ usuario.email }}</p>
                        <p v-if="usuario.cpf" class="text-xs text-gray-400 mb-2">CPF: {{ usuario.cpf }}</p>
                        <div class="flex items-center justify-between">
                            <span v-if="usuario.roles?.length" class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">
                                {{ usuario.roles[0].name }}
                            </span>
                            <span v-else></span>
                            <div class="flex space-x-3">
                                <Link :href="route('usuarios.edit', usuario.id)" class="text-sm text-indigo-600">Editar</Link>
                                <button @click="destroy(usuario.id)" class="text-sm text-red-600">Excluir</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="!usuarios?.length" class="bg-white rounded-lg shadow-sm p-8 text-center text-sm text-gray-500">
                        Nenhum usuário encontrado.
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">CPF</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ usuario.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ usuario.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ usuario.cpf || '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        <span v-if="usuario.roles?.length" class="inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                            {{ usuario.roles[0].name }}
                                        </span>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="usuario.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                            {{ usuario.status ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <Link :href="route('usuarios.edit', usuario.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                        <button @click="destroy(usuario.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </td>
                                </tr>
                                <tr v-if="!usuarios?.length">
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum usuário encontrado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
