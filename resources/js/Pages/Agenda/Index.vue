<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const props = defineProps({
    agenda: [Array, Object],
    profissionais: Array,
    filtros: Object,
    statusOptions: Array,
});

const data = ref(props.filtros.data);
const profissionalId = ref(props.filtros.profissional_id);
const visualizacao = ref(props.filtros.visualizacao);

function filtrar() {
    router.get(route('agenda.index'), {
        data: data.value,
        profissional_id: profissionalId.value || '',
        visualizacao: visualizacao.value,
    }, { preserveState: true, replace: true });
}

watch([data, profissionalId, visualizacao], () => filtrar());

function navDia(offset) {
    const d = new Date(data.value);
    d.setDate(d.getDate() + offset);
    data.value = d.toISOString().split('T')[0];
}

function navSemana(offset) {
    const d = new Date(data.value);
    d.setDate(d.getDate() + (offset * 7));
    data.value = d.toISOString().split('T')[0];
}

function hoje() {
    data.value = new Date().toISOString().split('T')[0];
}

function statusColor(statusValue) {
    const opt = props.statusOptions.find(s => s.value === statusValue);
    return opt ? opt.cor : '#6B7280';
}

function statusLabel(statusValue) {
    const opt = props.statusOptions.find(s => s.value === statusValue);
    return opt ? opt.label : statusValue;
}

function alterarStatus(agendamentoId, novoStatus) {
    router.put(route('agendamentos.status', agendamentoId), { status: novoStatus }, { preserveScroll: true });
}

const confirmState = ref({ show: false, title: '', message: '', action: null });

function cancelar(agendamentoId) {
    confirmState.value = {
        show: true,
        title: 'Cancelar Agendamento',
        message: 'Confirma o cancelamento deste agendamento?',
        action: (callbacks) => router.delete(route('agendamentos.cancelar', agendamentoId), { 
            preserveScroll: true,
            ...callbacks 
        })
    };
}

function formatarData(dataStr) {
    return new Date(dataStr + 'T00:00:00').toLocaleDateString('pt-BR');
}

const isSemana = computed(() => visualizacao.value === 'semana');

// Ordenar agendamentos do dia: "finalizado" vai pro final da lista
const sortedAgenda = computed(() => {
    if (isSemana.value || !props.agenda) return [];
    
    return [...props.agenda].sort((a, b) => {
        if (a.status === 'finalizado' && b.status !== 'finalizado') return 1;
        if (a.status !== 'finalizado' && b.status === 'finalizado') return -1;
        return 0; // mantém a ordem original (por hora) se ambos são ou não são finalizados
    });
});

function registrarChegada(agendamentoId) {
    router.put(route('agendamentos.chegada', agendamentoId), {}, { preserveScroll: true });
}

function desfazerChegada(agendamentoId) {
    confirmState.value = {
        show: true,
        title: 'Desmarcar Chegada',
        message: 'Deseja desmarcar a chegada deste paciente?',
        action: (callbacks) => router.delete(route('agendamentos.chegada.desfazer', agendamentoId), { 
            preserveScroll: true,
            ...callbacks 
        })
    };
}

// Gera uma cor fixa baseada no ID Numérico do Profissional para diferenciar as agendas
function getColorFromId(id) {
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];
    return colors[(id || 0) % colors.length];
}
</script>



<template>
    <Head title="Agenda" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Agenda</h2>
                <div class="flex space-x-2">
                    <Link :href="route('disponibilidades.index')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        Disponibilidades
                    </Link>
                    <Link :href="route('bloqueios.index')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                        Bloqueios
                    </Link>
                    <Link :href="route('agendamentos.create', { data: data, profissional_id: profissionalId })" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                        + Novo Agendamento
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Filtros -->
                <div class="mb-6 flex flex-wrap items-center gap-3 bg-white p-4 rounded-lg shadow-sm">
                    <!-- Navegação de data -->
                    <div class="flex items-center space-x-1">
                        <button @click="isSemana ? navSemana(-1) : navDia(-1)" class="rounded-md bg-gray-100 px-3 py-2 text-sm hover:bg-gray-200 transition">◀</button>
                        <button @click="hoje()" class="rounded-md bg-indigo-100 px-3 py-2 text-sm text-indigo-700 hover:bg-indigo-200 transition">Hoje</button>
                        <button @click="isSemana ? navSemana(1) : navDia(1)" class="rounded-md bg-gray-100 px-3 py-2 text-sm hover:bg-gray-200 transition">▶</button>
                    </div>

                    <input type="date" v-model="data" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />

                    <select v-model="profissionalId" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option :value="null">Visão Consolidada (Todas as Permissões)</option>
                        <option v-for="p in profissionais" :key="p.id" :value="p.id">{{ p.user?.name }} (Minha Agenda)</option>
                    </select>

                    <div class="flex rounded-md shadow-sm">
                        <button @click="visualizacao = 'dia'" :class="visualizacao === 'dia' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="rounded-l-md border px-3 py-2 text-sm transition">Dia</button>
                        <button @click="visualizacao = 'semana'" :class="visualizacao === 'semana' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="rounded-r-md border border-l-0 px-3 py-2 text-sm transition">Semana</button>
                    </div>
                </div>

                <!-- Vista Dia -->
                <div v-if="!isSemana" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="border-b bg-gray-50 px-6 py-3">
                        <h3 class="text-sm font-medium text-gray-700">{{ formatarData(data) }}</h3>
                    </div>
                    <div v-if="agenda?.length" class="divide-y divide-gray-100 relative">
                        <TransitionGroup name="list">
                            <div v-for="ag in sortedAgenda" :key="ag.id" :class="['flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-6 py-4 transition bg-white w-full', ag.status === 'finalizado' ? 'opacity-50' : 'hover:bg-gray-50']">
                                <div class="flex items-center space-x-4">
                                    <div class="text-center min-w-[70px]">
                                        <div class="text-sm font-semibold text-gray-900">{{ ag.hora_inicio?.substring(0,5) }}</div>
                                        <div class="text-xs text-gray-400">{{ ag.hora_fim?.substring(0,5) }}</div>
                                    </div>
                                    <div class="h-10 w-1 rounded-full" :style="{ backgroundColor: statusColor(ag.status) }"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                            {{ ag.paciente?.nome }}
                                            <div v-if="ag.hora_chegada" class="inline-flex items-center rounded-md bg-emerald-50 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 overflow-hidden">
                                                <span class="px-2 py-1">Chegou às {{ ag.hora_chegada.substring(0,5) }}</span>
                                                <button @click="desfazerChegada(ag.id)" class="px-1.5 py-1 hover:bg-emerald-100/80 border-l border-emerald-600/20 transition-colors" title="Desfazer">
                                                    ✕
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <span v-if="!profissionalId" class="w-2 h-2 rounded-full inline-block mr-1" :style="{ backgroundColor: getColorFromId(ag.profissional_id) }"></span>
                                            {{ ag.profissional?.user?.name }} <span v-if="ag.sala" class="ml-1">· {{ ag.sala?.nome }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 sm:justify-end w-full sm:w-auto">
                                    <button v-if="!ag.hora_chegada && ag.status !== 'finalizado' && ag.status !== 'cancelado'" @click="registrarChegada(ag.id)" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 border border-indigo-200 bg-indigo-50 rounded px-3 py-1.5 transition whitespace-nowrap" title="Marcar que o paciente chegou à clínica">
                                        Marcar chegada
                                    </button>
                                    <select @change="alterarStatus(ag.id, $event.target.value)" class="rounded-md border-gray-300 text-xs py-1.5 pl-2 pr-7 focus:border-indigo-500 focus:ring-indigo-500 grow sm:grow-0">
                                        <option v-for="st in statusOptions" :key="st.value" :value="st.value" :selected="st.value === ag.status">{{ st.label }}</option>
                                    </select>
                                    <button @click="cancelar(ag.id)" class="text-xs text-red-500 hover:text-red-700 px-2 py-1.5 border border-transparent" title="Cancelar">✕</button>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
                    <div v-else class="px-6 py-12 text-center text-gray-500 text-sm">Nenhum agendamento para esta data.</div>
                </div>

                <!-- Vista Semana -->
                <div v-else class="grid grid-cols-1 md:grid-cols-7 gap-4 md:gap-2">
                    <div v-for="dia in agenda" :key="dia.data" class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gray-50 border-b px-3 py-2 text-center">
                            <div class="text-xs font-medium text-gray-500 uppercase">{{ dia.nome_dia }}</div>
                            <div class="text-sm font-semibold text-gray-900">{{ dia.formatada }}</div>
                        </div>
                        <div class="p-2 space-y-1 min-h-[120px]">
                            <div v-for="ag in dia.agendamentos" :key="ag.id" class="rounded-md p-2 text-xs cursor-pointer hover:opacity-80 transition" :style="{ backgroundColor: statusColor(ag.status) + '20', borderLeft: '3px solid ' + statusColor(ag.status) }">
                                <div class="font-medium text-gray-900">{{ ag.hora_inicio?.substring(0,5) }}</div>
                                <div class="text-gray-600 truncate">{{ ag.paciente?.nome }}</div>
                                <div class="text-gray-400 truncate flex items-center mt-0.5">
                                    <span v-if="!profissionalId" class="w-1.5 h-1.5 rounded-full inline-block mr-1 flex-shrink-0" :style="{ backgroundColor: getColorFromId(ag.profissional_id) }"></span>
                                    <span class="truncate">{{ ag.profissional?.user?.name }}</span>
                                </div>
                            </div>
                            <div v-if="!dia.agendamentos?.length" class="text-center text-gray-300 text-xs py-4">—</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDialog
            :show="confirmState.show"
            :title="confirmState.title"
            :message="confirmState.message"
            @close="confirmState.show = false"
            @confirm="confirmState.action"
        />
    </AuthenticatedLayout>
</template>
