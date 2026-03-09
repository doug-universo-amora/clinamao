<script setup>
import PortalLayout from '@/Layouts/PortalLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    agendamentos: Object,
    tab: String,
});

function setTab(tabName) {
    router.get(route('portal.agendamentos'), { tab: tabName }, { preserveState: true });
}

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
    return parseSafeDate(dateStr).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatTime(timeStr) {
    if (!timeStr) return '';
    return timeStr.substring(0, 5);
}

function dayOfWeek(dateStr) {
    if (!dateStr) return '';
    return parseSafeDate(dateStr).toLocaleDateString('pt-BR', { weekday: 'long' });
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
        'agendado': 'bg-blue-100 text-blue-700', 'confirmado': 'bg-emerald-100 text-emerald-700',
        'em_atendimento': 'bg-amber-100 text-amber-700', 'finalizado': 'bg-gray-100 text-gray-600',
        'faltou': 'bg-red-100 text-red-700', 'cancelado': 'bg-red-100 text-red-600',
        'reagendado': 'bg-purple-100 text-purple-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-600';
}
</script>

<template>
    <PortalLayout>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Minhas Consultas</h1>

            <!-- Tabs -->
            <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
                <button
                    @click="setTab('proximas')"
                    :class="[
                        'px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                        tab === 'proximas'
                            ? 'bg-white text-teal-700 shadow-sm'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                >
                    📅 Próximas
                </button>
                <button
                    @click="setTab('historico')"
                    :class="[
                        'px-5 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                        tab === 'historico'
                            ? 'bg-white text-teal-700 shadow-sm'
                            : 'text-gray-500 hover:text-gray-700'
                    ]"
                >
                    📋 Histórico
                </button>
            </div>

            <!-- List -->
            <div v-if="agendamentos.data.length === 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-16 text-center">
                <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500">
                    {{ tab === 'proximas' ? 'Nenhuma consulta agendada.' : 'Nenhuma consulta no histórico.' }}
                </p>
            </div>

            <div v-else class="space-y-3">
                <Link
                    v-for="ag in agendamentos.data"
                    :key="ag.id"
                    :href="route('portal.agendamentos.show', ag.id)"
                    class="block bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4 hover:shadow-md hover:border-teal-200 transition-all duration-200 group"
                >
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="shrink-0 w-14 h-14 rounded-xl bg-teal-50 group-hover:bg-teal-100 flex flex-col items-center justify-center transition-colors">
                                <span class="text-xs font-medium text-teal-600 uppercase">
                                    {{ dayOfWeek(ag.data).substring(0, 3) }}
                                </span>
                                <span class="text-lg font-bold text-teal-700">
                                    {{ parseSafeDate(ag.data).getDate() }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate">
                                    {{ ag.profissional?.user?.name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ formatDate(ag.data) }} · {{ formatTime(ag.hora_inicio) }} - {{ formatTime(ag.hora_fim) }}
                                </p>
                                <p v-if="ag.profissional?.especialidade" class="text-xs text-gray-400 mt-0.5">
                                    {{ ag.profissional.especialidade }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span :class="[statusColor(ag.status), 'px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap']">
                                {{ statusLabel(ag.status) }}
                            </span>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-teal-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="agendamentos.links && agendamentos.last_page > 1" class="flex justify-center gap-1 mt-8">
                <template v-for="link in agendamentos.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            link.active
                                ? 'bg-teal-600 text-white'
                                : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'
                        ]"
                        v-html="link.label"
                        preserve-state
                    />
                    <span
                        v-else
                        class="px-3 py-2 text-sm text-gray-400"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </PortalLayout>
</template>
