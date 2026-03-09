<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    profissional: Object,
});

const form = useForm({
    especialidade: props.profissional.especialidade,
    registro_conselho: props.profissional.registro_conselho || '',
    duracao_consulta: props.profissional.duracao_consulta,
    status: props.profissional.status,
});

function submit() {
    form.put(route('profissionais.update', props.profissional.id));
}
</script>

<template>
    <Head title="Editar Profissional" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Profissional: {{ profissional.user?.name }}</h2>
                <Link :href="route('profissionais.index')" class="text-sm text-indigo-600 hover:text-indigo-900">← Voltar</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="especialidade" value="Especialidade *" />
                            <TextInput id="especialidade" type="text" class="mt-1 block w-full" v-model="form.especialidade" required />
                            <InputError :message="form.errors.especialidade" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="registro_conselho" value="Registro no Conselho" />
                                <TextInput id="registro_conselho" type="text" class="mt-1 block w-full" v-model="form.registro_conselho" />
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
                            <PrimaryButton :disabled="form.processing">Atualizar Profissional</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
