<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showSenha = ref(false);

const props = defineProps({
    paciente: Object,
    statusOptions: Array,
});

const form = useForm({
    nome: props.paciente.nome,
    cpf: props.paciente.cpf || '',
    telefone: props.paciente.telefone || '',
    email: props.paciente.email || '',
    data_nascimento: props.paciente.data_nascimento?.split('T')[0] || '',
    observacoes: props.paciente.observacoes || '',
    status: props.paciente.status,
    senha: '',
});

const temSenha = !!props.paciente.tem_senha;

function submit() {
    form.put(route('pacientes.update', props.paciente.id));
}
</script>

<template>
    <Head title="Editar Paciente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Paciente</h2>
                <Link :href="route('pacientes.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="nome" value="Nome *" />
                            <TextInput id="nome" type="text" class="mt-1 block w-full" v-model="form.nome" required />
                            <InputError :message="form.errors.nome" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="cpf" value="CPF" />
                                <TextInput id="cpf" type="text" class="mt-1 block w-full" v-model="form.cpf" />
                                <InputError :message="form.errors.cpf" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="telefone" value="Telefone" />
                                <TextInput id="telefone" type="text" class="mt-1 block w-full" v-model="form.telefone" />
                                <InputError :message="form.errors.telefone" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="data_nascimento" value="Data de Nascimento" />
                                <TextInput id="data_nascimento" type="date" class="mt-1 block w-full" v-model="form.data_nascimento" />
                                <InputError :message="form.errors.data_nascimento" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="status" value="Status" />
                            <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel for="observacoes" value="Observações" />
                            <textarea id="observacoes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3" v-model="form.observacoes"></textarea>
                            <InputError :message="form.errors.observacoes" class="mt-2" />
                        </div>

                        <!-- Acesso ao Portal -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Acesso ao Portal do Paciente</h3>
                            <p class="text-xs text-gray-500 mb-3">
                                <template v-if="temSenha">
                                    ✅ Este paciente já possui acesso ao portal. Preencha abaixo apenas para <strong>alterar</strong> a senha.
                                </template>
                                <template v-else>
                                    Defina uma senha para permitir que o paciente acesse o portal. Deixe em branco para não conceder acesso.
                                </template>
                            </p>
                            <div class="max-w-sm">
                                <InputLabel for="senha" :value="temSenha ? 'Nova Senha (deixe em branco para manter)' : 'Senha do Portal'" />
                                <div class="relative mt-1">
                                    <TextInput id="senha" :type="showSenha ? 'text' : 'password'" class="block w-full pr-10" v-model="form.senha" placeholder="Mínimo 6 caracteres" />
                                    <button type="button" @click="showSenha = !showSenha" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg v-if="!showSenha" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18"/></svg>
                                    </button>
                                </div>
                                <InputError :message="form.errors.senha" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton :disabled="form.processing">Atualizar Paciente</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
