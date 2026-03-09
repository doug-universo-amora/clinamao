<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    paciente: Object,
});

function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR');
}

function statusLabel(status) {
    const labels = { ativo: 'Ativo', bloqueado: 'Bloqueado', alta: 'Alta' };
    return labels[status] || status;
}

function statusClass(status) {
    const classes = {
        ativo: 'bg-green-100 text-green-800',
        bloqueado: 'bg-red-100 text-red-800',
        alta: 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

function agendamentoStatusLabel(status) {
    const labels = {
        agendado: 'Agendado', confirmado: 'Confirmado', em_atendimento: 'Em Atend.',
        finalizado: 'Finalizado', faltou: 'Faltou', cancelado: 'Cancelado', reagendado: 'Reagendado',
    };
    return labels[status] || status;
}
</script>

<template>
    <Head :title="paciente.nome" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 truncate">{{ paciente.nome }}</h2>
                <div class="flex space-x-3">
                    <Link :href="route('pacientes.edit', paciente.id)" class="text-sm text-indigo-600 hover:text-indigo-900">Editar</Link>
                    <Link :href="route('pacientes.index')" class="text-sm text-gray-600 hover:text-gray-900">← Voltar</Link>
                </div>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <!-- Dados do Paciente -->
                <div class="overflow-hidden bg-white p-4 sm:p-6 shadow-sm sm:rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dados Pessoais</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CPF</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ paciente.cpf || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ paciente.telefone || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ paciente.email || '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(paciente.data_nascimento) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span :class="statusClass(paciente.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                    {{ statusLabel(paciente.status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                    <div v-if="paciente.observacoes" class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Observações</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ paciente.observacoes }}</dd>
                    </div>
                </div>

                <!-- Histórico de Agendamentos -->
                <div class="overflow-hidden bg-white p-4 sm:p-6 shadow-sm sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Agendamentos</h3>

                    <!-- Mobile Cards -->
                    <div v-if="paciente.agendamentos?.length" class="sm:hidden space-y-3">
                        <div v-for="ag in paciente.agendamentos" :key="ag.id" class="border rounded-md p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ formatDate(ag.data) }}</span>
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                    {{ agendamentoStatusLabel(ag.status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ ag.hora_inicio?.substring(0, 5) }} - {{ ag.hora_fim?.substring(0, 5) }}</p>
                            <p class="text-xs text-gray-400">{{ ag.profissional?.user?.name || '—' }}</p>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div v-if="paciente.agendamentos?.length" class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Horário</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Profissional</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="ag in paciente.agendamentos" :key="ag.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ formatDate(ag.data) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ ag.hora_inicio?.substring(0, 5) }} - {{ ag.hora_fim?.substring(0, 5) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ ag.profissional?.user?.name || '—' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                            {{ agendamentoStatusLabel(ag.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-if="!paciente.agendamentos?.length" class="text-sm text-gray-500">Nenhum agendamento encontrado.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
