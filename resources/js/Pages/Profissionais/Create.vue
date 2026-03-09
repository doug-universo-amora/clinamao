<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    usuarios: Array,
});

const form = useForm({
    user_id: '',
    especialidade: '',
    registro_conselho: '',
    duracao_consulta: 30,
    status: true,
});

function submit() {
    form.post(route('profissionais.store'));
}
</script>

<template>
    <Head title="Novo Profissional" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Profissional</h2>
                <Link :href="route('profissionais.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="user_id" value="Usuário *" />
                            <select id="user_id" v-model="form.user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Selecione um usuário...</option>
                                <option v-for="u in usuarios" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
                            </select>
                            <InputError :message="form.errors.user_id" class="mt-2" />
                            <p v-if="!usuarios?.length" class="mt-1 text-sm text-amber-600">Não há usuários disponíveis. Crie um usuário primeiro.</p>
                        </div>

                        <div>
                            <InputLabel for="especialidade" value="Especialidade *" />
                            <TextInput id="especialidade" type="text" class="mt-1 block w-full" v-model="form.especialidade" placeholder="Ex: Cardiologia, Psicologia..." required />
                            <InputError :message="form.errors.especialidade" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="registro_conselho" value="Registro no Conselho" />
                                <TextInput id="registro_conselho" type="text" class="mt-1 block w-full" v-model="form.registro_conselho" placeholder="CRM, CRP, CREFITO..." />
                                <InputError :message="form.errors.registro_conselho" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="duracao_consulta" value="Duração da Consulta (min)" />
                                <TextInput id="duracao_consulta" type="number" class="mt-1 block w-full" v-model="form.duracao_consulta" min="5" max="480" />
                                <InputError :message="form.errors.duracao_consulta" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.status" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <span class="ml-2 text-sm text-gray-600">Ativo</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton :disabled="form.processing">Salvar Profissional</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
