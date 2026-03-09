<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
    profissional: Object,
    outrosProfissionais: Array,
    acessosRecebidos: Object, // Agora é um objeto { [concedente_id]: { agenda: {...}, bloqueio: {...}, disponibilidade: {...} } }
});

const defaultPermissoes = () => ({
    agenda: { ler: true, criar: false, editar: false, excluir: false },
    bloqueio: { ler: true, criar: false, editar: false, excluir: false },
    disponibilidade: { ler: false, criar: false, editar: false, excluir: false }
});

const acessosAtivos = ref({});
const permissoesObj = ref({});

onMounted(() => {
    // Inicializa o state baseado no que veio do backend
    props.outrosProfissionais.forEach(outro => {
        if (props.acessosRecebidos[outro.id]) {
            acessosAtivos.value[outro.id] = true;
            permissoesObj.value[outro.id] = props.acessosRecebidos[outro.id];
        } else {
            acessosAtivos.value[outro.id] = false;
            permissoesObj.value[outro.id] = defaultPermissoes();
        }
    });
});

const form = useForm({
    acessos: {}
});

function toggleAcesso(id) {
    if (!acessosAtivos.value[id]) {
        // Se desativou, podemos resetar para o default por segurança
        permissoesObj.value[id] = defaultPermissoes();
    }
}

const saveAcessos = () => {
    // Constrói o payload final enviando APENAS os profissionais que tem o switch principal "ativo"
    const payload = {};
    for (const [id, ativo] of Object.entries(acessosAtivos.value)) {
        if (ativo) {
            payload[id] = permissoesObj.value[id];
        }
    }
    
    form.acessos = payload;
    form.post(route('profissionais.acessos.sync', props.profissional.id));
};
</script>

<template>
    <Head title="Gerenciar Acessos à Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                    <span>Permissões de Agenda</span>
                    <span class="text-gray-400">/</span>
                    <span class="text-indigo-600">{{ profissional.user.name }}</span>
                </h2>
                <Link
                    :href="route('profissionais.index')"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50 transition"
                >
                    Voltar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white px-4 py-6 shadow sm:rounded-lg sm:p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Agendas Compatilhadas</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Configure de forma granular quais ações o(a) profissional <strong>{{ profissional.user.name }}</strong> pode realizar na agenda de seus colegas.
                        </p>
                    </div>

                    <form @submit.prevent="saveAcessos" class="space-y-6">
                        <div v-for="outro in outrosProfissionais" :key="outro.id" class="border border-gray-200 rounded-lg overflow-hidden transition" :class="acessosAtivos[outro.id] ? 'ring-1 ring-indigo-500' : ''">
                            
                            <!-- Header / Toggle Principal -->
                            <div class="flex items-center justify-between bg-gray-50 p-4 border-b border-gray-200" :class="acessosAtivos[outro.id] ? 'bg-indigo-50/50' : ''">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                        Agenda de: {{ outro.user.name }}
                                        <span v-if="outro.especialidade" class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ outro.especialidade }}</span>
                                    </h4>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="acessosAtivos[outro.id]" @change="toggleAcesso(outro.id)" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                    <span class="ml-3 text-sm font-medium" :class="acessosAtivos[outro.id] ? 'text-indigo-600' : 'text-gray-500'">{{ acessosAtivos[outro.id] ? 'Acesso Concedido' : 'Sem Acesso' }}</span>
                                </label>
                            </div>

                            <!-- Matriz de Permissões Granulares (Exibida Apenas se Ativo) -->
                            <div v-if="acessosAtivos[outro.id] && permissoesObj[outro.id]" class="p-4 bg-white">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Módulo</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Visualizar (Ler)</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Adicionar (Criar)</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Modificar (Editar)</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Remover (Excluir)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <!-- Row: Agenda -->
                                            <tr v-if="permissoesObj[outro.id].agenda" class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Agendamentos</td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].agenda.ler" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].agenda.criar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].agenda.editar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].agenda.excluir" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                            </tr>
                                            <!-- Row: Bloqueios -->
                                            <tr v-if="permissoesObj[outro.id].bloqueio" class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Bloqueios de Agenda</td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].bloqueio.ler" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].bloqueio.criar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].bloqueio.editar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].bloqueio.excluir" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                            </tr>
                                            <!-- Row: Disponibilidades -->
                                            <tr v-if="permissoesObj[outro.id].disponibilidade" class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Disponibilidade (Escala)</td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].disponibilidade.ler" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].disponibilidade.criar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].disponibilidade.editar" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                                <td class="px-4 py-3 text-center"><input type="checkbox" v-model="permissoesObj[outro.id].disponibilidade.excluir" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <div v-if="outrosProfissionais.length === 0" class="text-gray-500 italic text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            Não há outros profissionais ativos cadastrados na clínica para conceder acesso.
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Salvar Permissões
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
