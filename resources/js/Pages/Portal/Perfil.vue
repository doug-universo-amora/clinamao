<script setup>
import PortalLayout from '@/Layouts/PortalLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    paciente: Object,
});

// Profile form
const profileForm = useForm({
    email: props.paciente.email || '',
    telefone: props.paciente.telefone || '',
});

// Password form
const passwordForm = useForm({
    senha_atual: '',
    nova_senha: '',
    nova_senha_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);

function updateProfile() {
    profileForm.put(route('portal.perfil.update'));
}

function changePassword() {
    passwordForm.put(route('portal.perfil.senha'), {
        onSuccess: () => passwordForm.reset(),
    });
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('pt-BR');
}
</script>

<template>
    <PortalLayout>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-gray-800 mb-8">Meu Perfil</h1>

            <!-- Info Card (read-only) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informações Pessoais</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nome</p>
                        <p class="text-gray-800 font-medium mt-1">{{ paciente.nome }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">CPF</p>
                        <p class="text-gray-800 font-medium mt-1">{{ paciente.cpf }}</p>
                    </div>
                    <div v-if="paciente.data_nascimento">
                        <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Data de Nascimento</p>
                        <p class="text-gray-800 font-medium mt-1">{{ formatDate(paciente.data_nascimento) }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Dados de Contato</h2>
                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input
                            v-model="profileForm.email"
                            type="email"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                            placeholder="seu@email.com"
                        />
                        <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">{{ profileForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input
                            v-model="profileForm.telefone"
                            type="text"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                            placeholder="(00) 00000-0000"
                        />
                        <p v-if="profileForm.errors.telefone" class="mt-1 text-sm text-red-600">{{ profileForm.errors.telefone }}</p>
                    </div>
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-semibold rounded-xl shadow hover:shadow-lg hover:from-teal-600 hover:to-emerald-700 transition-all disabled:opacity-50"
                        >
                            {{ profileForm.processing ? 'Salvando...' : 'Salvar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Alterar Senha</h2>
                <form @submit.prevent="changePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                        <div class="relative">
                            <input
                                v-model="passwordForm.senha_atual"
                                :type="showCurrentPassword ? 'text' : 'password'"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                            />
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="!showCurrentPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.senha_atual" class="mt-1 text-sm text-red-600">{{ passwordForm.errors.senha_atual }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <div class="relative">
                            <input
                                v-model="passwordForm.nova_senha"
                                :type="showNewPassword ? 'text' : 'password'"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                            />
                            <button type="button" @click="showNewPassword = !showNewPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="!showNewPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <p v-if="passwordForm.errors.nova_senha" class="mt-1 text-sm text-red-600">{{ passwordForm.errors.nova_senha }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <input
                            v-model="passwordForm.nova_senha_confirmation"
                            type="password"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"
                        />
                    </div>
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="px-6 py-2.5 bg-gray-800 text-white font-semibold rounded-xl shadow hover:bg-gray-900 transition-all disabled:opacity-50"
                        >
                            {{ passwordForm.processing ? 'Alterando...' : 'Alterar Senha' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PortalLayout>
</template>
