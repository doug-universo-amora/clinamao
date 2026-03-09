<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    cpf: '',
    senha: '',
    lembrar: false,
});

const showPassword = ref(false);

function submit() {
    form.post(route('portal.login.post'), {
        onFinish: () => form.reset('senha'),
    });
}

// Máscara simples de CPF
function maskCpf(event) {
    let v = event.target.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    form.cpf = v;
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-teal-600 via-emerald-600 to-teal-700 flex items-center justify-center p-4">
        <!-- Decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-emerald-400/10 blur-3xl"></div>
        </div>

        <div class="relative w-full max-w-md">
            <!-- Logo / Brand -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-sm mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Portal do Paciente</h1>
                <p class="text-teal-100 mt-1 text-sm">Acesse suas consultas e informações</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- CPF -->
                    <div>
                        <label for="cpf" class="block text-sm font-semibold text-gray-700 mb-1.5">CPF</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input
                                id="cpf"
                                type="text"
                                :value="form.cpf"
                                @input="maskCpf"
                                placeholder="000.000.000-00"
                                class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-800 placeholder-gray-400 transition-all"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.cpf }"
                                maxlength="14"
                                autofocus
                            />
                        </div>
                        <p v-if="form.errors.cpf" class="mt-1.5 text-sm text-red-600">{{ form.errors.cpf }}</p>
                    </div>

                    <!-- Senha -->
                    <div>
                        <label for="senha" class="block text-sm font-semibold text-gray-700 mb-1.5">Senha</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="senha"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.senha"
                                placeholder="••••••••"
                                class="w-full pl-11 pr-12 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-gray-800 placeholder-gray-400 transition-all"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': form.errors.senha }"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <p v-if="form.errors.senha" class="mt-1.5 text-sm text-red-600">{{ form.errors.senha }}</p>
                    </div>

                    <!-- Lembrar-me -->
                    <div class="flex items-center">
                        <input
                            id="lembrar"
                            type="checkbox"
                            v-model="form.lembrar"
                            class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500 transition-colors"
                        />
                        <label for="lembrar" class="ml-2 text-sm text-gray-600">Lembrar de mim</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3 px-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-teal-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="!form.processing">Entrar</span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Entrando...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center mt-6 text-sm text-teal-100/80">
                Área exclusiva para pacientes da clínica
            </p>
        </div>
    </div>
</template>
