<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { ref } from 'vue';

const props = defineProps({
    profissionais: Array,
});

const confirmState = ref({ show: false, title: '', message: '', action: null });

function destroy(id) {
    confirmState.value = {
        show: true,
        title: 'Excluir Profissional',
        message: 'Tem certeza que deseja excluir este profissional?',
        action: (callbacks) => router.delete(route('profissionais.destroy', id), {
            preserveScroll: true,
            ...callbacks
        }),
    };
}
</script>

<template>
    <Head title="Profissionais" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Profissionais</h2>
                <Link
                    :href="route('profissionais.create')"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition"
                >
                    + Novo Profissional
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Mobile Cards -->
                <div class="sm:hidden space-y-3">
                    <div v-for="prof in profissionais" :key="prof.id" class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-semibold text-gray-900">{{ prof.user?.name }}</span>
                            <span :class="prof.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ prof.status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-1">{{ prof.user?.email }}</p>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="text-xs text-gray-600">{{ prof.especialidade }}</span>
                            <span v-if="prof.registro_conselho" class="text-xs text-gray-400">· {{ prof.registro_conselho }}</span>
                            <span class="text-xs text-gray-400">· {{ prof.duracao_consulta }} min</span>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <Link v-if="$page.props.auth.user.permissions.includes('profissionais.editar')" :href="route('profissionais.acessos', prof.id)" class="text-sm text-emerald-600 font-medium hover:underline">Acessos</Link>
                            <Link :href="route('profissionais.edit', prof.id)" class="text-sm text-indigo-600 hover:underline">Editar</Link>
                            <button @click="destroy(prof.id)" class="text-sm text-red-600 hover:underline">Excluir</button>
                        </div>
                    </div>
                    <div v-if="!profissionais?.length" class="bg-white rounded-lg shadow-sm p-8 text-center text-sm text-gray-500">
                        Nenhum profissional encontrado.
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
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Especialidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Conselho</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Duração</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="prof in profissionais" :key="prof.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ prof.user?.name }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ prof.user?.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ prof.especialidade }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ prof.registro_conselho || '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ prof.duracao_consulta }} min</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        <span :class="prof.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                            {{ prof.status ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <Link v-if="$page.props.auth.user.permissions.includes('profissionais.editar')" :href="route('profissionais.acessos', prof.id)" class="text-emerald-600 hover:text-emerald-900 mr-4 font-semibold">Acessos</Link>
                                        <Link :href="route('profissionais.edit', prof.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</Link>
                                        <button @click="destroy(prof.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </td>
                                </tr>
                                <tr v-if="!profissionais?.length">
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum profissional encontrado.</td>
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
