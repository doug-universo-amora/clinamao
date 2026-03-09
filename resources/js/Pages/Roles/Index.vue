<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { ref } from 'vue';

const props = defineProps({
    roles: Object,
});

const confirmState = ref({ show: false, title: '', message: '', action: null });

function destroy(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Perfil',
        message: 'Tem certeza que deseja excluir este perfil? Esta ação não pode ser desfeita e só funcionará se nenhum usuário estiver vinculado a ele.',
        action: (callbacks) => router.delete(route('roles.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}
</script>

<template>
    <Head title="Perfis de Acesso" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Perfis de Acesso</h2>
                <Link
                    v-if="$can('roles.criar')"
                    :href="route('roles.create')"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition"
                >
                    + Novo Perfil
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>
                <div v-if="$page.props.flash?.error" class="mb-4 rounded-md bg-red-50 p-4">
                    <p class="text-sm font-medium text-red-800">{{ $page.props.flash.error }}</p>
                </div>

                <!-- Mobile Cards -->
                <div class="sm:hidden space-y-3 mb-6">
                    <div v-for="role in roles.data" :key="role.id" class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-900">
                                {{ role.name }}
                                <span v-if="role.name === 'Administrador'" class="ml-2 inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800">
                                    Protegido
                                </span>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Usuários Vinculados: {{ role.users_count }}</p>
                        <div class="flex items-center justify-end space-x-3">
                            <Link v-if="$can('roles.editar')" :href="route('roles.edit', role.id)" class="text-sm text-indigo-600 font-medium">Editar</Link>
                            <button v-if="$can('roles.excluir') && role.name !== 'Administrador'" @click="destroy(role.id)" class="text-sm text-red-600 font-medium disabled:opacity-50" :disabled="role.users_count > 0">Excluir</button>
                        </div>
                    </div>
                    <div v-if="!roles.data?.length" class="bg-white rounded-lg shadow-sm p-8 text-center text-sm text-gray-500">
                        Nenhum perfil encontrado.
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden sm:block overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nome do Perfil</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Usuários Vinculados</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ role.name }}
                                        <span v-if="role.name === 'Administrador'" class="ml-2 inline-flex items-center rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                            Protegido
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ role.users_count }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <Link v-if="$can('roles.editar')" :href="route('roles.edit', role.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</Link>
                                        <button v-if="$can('roles.excluir') && role.name !== 'Administrador'" @click="destroy(role.id)" class="text-red-600 hover:text-red-900 disabled:opacity-50" :disabled="role.users_count > 0">Excluir</button>
                                    </td>
                                </tr>
                                <tr v-if="!roles.data?.length">
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum perfil encontrado.</td>
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
