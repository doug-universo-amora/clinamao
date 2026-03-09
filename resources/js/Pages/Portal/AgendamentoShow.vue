<script setup>
import PortalLayout from '@/Layouts/PortalLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    agendamento: Object,
});

function parseSafeDate(dateStr) {
    if (!dateStr) return new Date();
    if (dateStr.includes('/')) {
        const [d, m, y] = dateStr.split(' ')[0].split('/');
        return new Date(y, m - 1, d);
    }
    const match = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (match) {
        return new Date(parseInt(match[1], 10), parseInt(match[2], 10) - 1, parseInt(match[3], 10));
    }
    return new Date(dateStr);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return parseSafeDate(dateStr).toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
}

function formatTime(timeStr) {
    if (!timeStr) return '';
    return timeStr.substring(0, 5);
}

function statusLabel(status) {
    const labels = {
        'agendado': 'Agendado', 'confirmado': 'Confirmado', 'em_atendimento': 'Em Atendimento',
        'finalizado': 'Finalizado', 'faltou': 'Faltou', 'cancelado': 'Cancelado', 'reagendado': 'Reagendado',
    };
    return labels[status] || status;
}

function statusColor(status) {
    const colors = {
        'agendado': 'bg-blue-100 text-blue-700 border-blue-200',
        'confirmado': 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'em_atendimento': 'bg-amber-100 text-amber-700 border-amber-200',
        'finalizado': 'bg-gray-100 text-gray-600 border-gray-200',
        'faltou': 'bg-red-100 text-red-700 border-red-200',
        'cancelado': 'bg-red-50 text-red-600 border-red-200',
        'reagendado': 'bg-purple-100 text-purple-700 border-purple-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-600 border-gray-200';
}
</script>

<template>
    <PortalLayout>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back -->
            <Link :href="route('portal.agendamentos')" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-teal-600 mb-6 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Voltar para consultas
            </Link>

            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header com status -->
                <div class="px-6 py-5 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-teal-100">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h1 class="text-xl font-bold text-gray-800">Detalhes da Consulta</h1>
                        <span :class="[statusColor(agendamento.status), 'px-4 py-1.5 rounded-full text-sm font-semibold border']">
                            {{ statusLabel(agendamento.status) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Data e Hora -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 capitalize">{{ formatDate(agendamento.data) }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ formatTime(agendamento.hora_inicio) }} - {{ formatTime(agendamento.hora_fim) }}
                            </p>
                        </div>
                    </div>

                    <!-- Profissional -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ agendamento.profissional?.user?.name }}</p>
                            <p class="text-sm text-gray-500" v-if="agendamento.profissional?.especialidade">
                                {{ agendamento.profissional.especialidade }}
                            </p>
                        </div>
                    </div>

                    <!-- Sala -->
                    <div v-if="agendamento.sala" class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ agendamento.sala.nome }}</p>
                            <p class="text-sm text-gray-500">Sala</p>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div v-if="agendamento.observacoes" class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">Observações</p>
                            <p class="text-sm text-gray-600">{{ agendamento.observacoes }}</p>
                        </div>
                    </div>
                </div>

                <!-- Histórico de reagendamentos -->
                <div v-if="agendamento.historico_reagendamentos?.length" class="border-t border-gray-100 px-6 py-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Histórico de Reagendamentos</h3>
                    <div class="space-y-2">
                        <div
                            v-for="(hist, i) in agendamento.historico_reagendamentos"
                            :key="i"
                            class="bg-gray-50 rounded-xl px-4 py-3 text-sm"
                        >
                            <p class="text-gray-600">
                                <span class="text-gray-400">De:</span>
                                {{ formatDate(hist.data_anterior) }} às {{ formatTime(hist.hora_anterior) }}
                            </p>
                            <p class="text-gray-600">
                                <span class="text-gray-400">Para:</span>
                                {{ formatDate(hist.nova_data) }} às {{ formatTime(hist.novo_horario) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
