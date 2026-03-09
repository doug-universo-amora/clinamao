<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    permissions: Object,
});

const form = useForm({
    name: '',
    permissions: [],
});

const selectAll = () => {
    form.permissions = Object.values(props.permissions)
        .flat()
        .map(p => p.name);
};

const deselectAll = () => {
    form.permissions = [];
};

const selectAllLists = () => {
    form.permissions = Object.values(props.permissions)
        .flat()
        .filter(p => p.name.endsWith('.listar') || p.name.endsWith('.visualizar'))
        .map(p => p.name);
};

const submit = () => {
    form.post(route('roles.store'));
};
</script>

<template>
    <Head title="Novo Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('roles.index')" class="text-gray-500 hover:text-gray-700">
                    &larr; Voltar
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Novo Perfil de Acesso
                </h2>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6 max-w-4xl">
                        
                        <!-- Nome -->
                        <div>
                            <InputLabel for="name" value="Nome do Perfil" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full max-w-md"
                                v-model="form.name"
                                required
                                autofocus
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <hr class="my-6 border-gray-200" />

                        <!-- Permissões Matrix -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Permissões do Perfil</h3>
                                <div class="flex gap-2">
                                    <button type="button" @click="selectAll" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded">Marcar Todos</button>
                                    <button type="button" @click="selectAllLists" class="text-xs font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-2 py-1 rounded">Apenas Listagem</button>
                                    <button type="button" @click="deselectAll" class="text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 px-2 py-1 rounded">Limpar</button>
                                </div>
                            </div>
                            <InputError class="mt-2 mb-4" :message="form.errors.permissions" />
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="(perms, group) in permissions" :key="group" class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                                    <h4 class="font-semibold text-gray-700 capitalize mb-3 border-b pb-2">{{ group }}</h4>
                                    <div class="space-y-2">
                                        <label v-for="permission in perms" :key="permission.id" class="flex items-center cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                :value="permission.name"
                                                v-model="form.permissions"
                                            >
                                            <span class="ml-2 text-sm text-gray-600">{{ permission.name.replace(group + '.', '') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Salvar Perfil
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
