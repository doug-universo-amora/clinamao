<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    cpf: '',
    email: '',
    telefone: '',
    password: '',
    password_confirmation: '',
    role: '',
    status: true,
});

function submit() {
    form.post(route('usuarios.store'));
}
</script>

<template>
    <Head title="Novo Usuário" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Usuário</h2>
                <Link :href="route('usuarios.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Nome *" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="cpf" value="CPF" />
                                <TextInput id="cpf" type="text" class="mt-1 block w-full" v-model="form.cpf" placeholder="000.000.000-00" />
                                <InputError :message="form.errors.cpf" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="telefone" value="Telefone" />
                                <TextInput id="telefone" type="text" class="mt-1 block w-full" v-model="form.telefone" placeholder="(00) 00000-0000" />
                                <InputError :message="form.errors.telefone" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="email" value="Email *" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="password" value="Senha *" />
                                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar Senha *" />
                                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="role" value="Papel (Role)" />
                                <select id="role" v-model="form.role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Selecione...</option>
                                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                                </select>
                                <InputError :message="form.errors.role" class="mt-2" />
                            </div>
                            <div class="flex items-center pt-7">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <span class="ml-2 text-sm text-gray-600">Ativo</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton :disabled="form.processing">
                                Salvar Usuário
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
