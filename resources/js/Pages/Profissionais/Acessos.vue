<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    profissional: Object,
    outrosProfissionais: Array,
    acessosRecebidos: Array,
});

const form = useForm({
    acessos: props.acessosRecebidos || []
});

const saveAcessos = () => {
    form.post(route('profissionais.acessos.sync', props.profissional.id));
};
</script>

<template>
    <Head title="Gerenciar Acessos à Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Gerenciar Acessos à Agenda - {{ profissional.user.name }}
                </h2>
                <Link
                    :href="route('profissionais.index')"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <div class="p-6">
            <div class="mx-auto max-w-7xl">
                <div class="overflow-hidden bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6 pb-12">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Agendas Compartilhadas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Selecione abaixo quais agendas de outros profissionais o <strong>{{ profissional.user.name }}</strong> terá permissão para visualizar e gerenciar.
                        </p>
                    </div>

                    <form @submit.prevent="saveAcessos">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <label v-for="outro in outrosProfissionais" :key="outro.id" class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input 
                                    type="checkbox" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 mr-3"
                                    :value="outro.id"
                                    v-model="form.acessos"
                                >
                                <div>
                                    <span class="block text-sm font-medium text-gray-900">{{ outro.user.name }}</span>
                                    <span class="block text-xs text-gray-500" v-if="outro.especialidade">{{ outro.especialidade }}</span>
                                </div>
                            </label>
                        </div>

                        <div v-if="outrosProfissionais.length === 0" class="text-gray-500 italic mt-4">
                            Não há outros profissionais ativos cadastrados na clínica para conceder acesso.
                        </div>

                        <div class="mt-8 flex justify-end">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Salvar Acessos
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
