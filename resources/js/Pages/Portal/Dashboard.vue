<script setup>
import PortalLayout from '@/Layouts/PortalLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    paciente: Object,
    proximasConsultas: Array,
    ultimaConsulta: Object,
    totalConsultas: Number,
    consultasFuturas: Number,
});

function parseSafeDate(dateStr) {
    if (!dateStr) return new Date();
    // Se vier DD/MM/YYYY
    if (dateStr.includes('/')) {
        const [d, m, y] = dateStr.split(' ')[0].split('/');
        return new Date(y, m - 1, d);
    }
    // Se vier YYYY-MM-DD...
    const match = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (match) {
        return new Date(parseInt(match[1], 10), parseInt(match[2], 10) - 1, parseInt(match[3], 10));
    }
    return new Date(dateStr);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return parseSafeDate(dateStr).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
}

function formatTime(timeStr) {
    if (!timeStr) return '';
    return timeStr.substring(0, 5);
}

function statusLabel(status) {
    const labels = {
        'agendado': 'Agendado',
        'confirmado': 'Confirmado',
        'em_atendimento': 'Em Atendimento',
        'finalizado': 'Finalizado',
        'faltou': 'Faltou',
        'cancelado': 'Cancelado',
        'reagendado': 'Reagendado',
    };
    return labels[status] || status;
}

function statusColor(status) {
    const colors = {
        'agendado': 'bg-blue-100 text-blue-700',
        'confirmado': 'bg-emerald-100 text-emerald-700',
        'em_atendimento': 'bg-amber-100 text-amber-700',
        'finalizado': 'bg-gray-100 text-gray-600',
        'faltou': 'bg-red-100 text-red-700',
        'cancelado': 'bg-red-100 text-red-600',
        'reagendado': 'bg-purple-100 text-purple-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-600';
}

function dayOfWeek(dateStr) {
    if (!dateStr) return '';
    return parseSafeDate(dateStr).toLocaleDateString('pt-BR', { weekday: 'long' });
}
</script>

<template>
    <PortalLayout>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome -->
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                    Olá, {{ paciente.nome?.split(' ')[0] }}! 👋
                </h1>
                <p class="text-gray-500 mt-1">Bem-vindo(a) ao seu portal de saúde.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <!-- Próximas consultas -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ consultasFuturas }}</p>
                            <p class="text-sm text-gray-500">Consultas agendadas</p>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800">{{ totalConsultas }}</p>
                            <p class="text-sm text-gray-500">Total de consultas</p>
                        </div>
                    </div>
                </div>

                <!-- Última consulta -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800" v-if="ultimaConsulta">
                                {{ formatDate(ultimaConsulta.data) }}
                            </p>
                            <p class="text-sm font-semibold text-gray-400" v-else>—</p>
                            <p class="text-sm text-gray-500">Última consulta</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas Consultas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Próximas Consultas</h2>
                    <Link :href="route('portal.agendamentos')" class="text-sm text-teal-600 hover:text-teal-700 font-medium transition-colors">
                        Ver todas →
                    </Link>
                </div>

                <div v-if="proximasConsultas.length === 0" class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 text-sm">Nenhuma consulta agendada.</p>
                </div>

                <div v-else class="divide-y divide-gray-50">
                    <div
                        v-for="consulta in proximasConsultas"
                        :key="consulta.id"
                        class="px-6 py-4 hover:bg-gray-50/50 transition-colors"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <!-- Date pill -->
                                <div class="shrink-0 w-14 h-14 rounded-xl bg-teal-50 flex flex-col items-center justify-center">
                                    <span class="text-xs font-medium text-teal-600 uppercase">
                                        {{ dayOfWeek(consulta.data).substring(0, 3) }}
                                    </span>
                                    <span class="text-lg font-bold text-teal-700">
                                        {{ parseSafeDate(consulta.data).getDate() }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 truncate">
                                        {{ consulta.profissional?.user?.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(consulta.data) }} · {{ formatTime(consulta.hora_inicio) }} - {{ formatTime(consulta.hora_fim) }}
                                    </p>
                                </div>
                            </div>
                            <span :class="[statusColor(consulta.status), 'px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap']">
                                {{ statusLabel(consulta.status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PortalLayout>
</template>
