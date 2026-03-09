<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showMobileMenu = ref(false);
const page = usePage();
const paciente = page.props.auth?.paciente;

const navigation = [
    { name: 'Dashboard', route: 'portal.dashboard', icon: 'home' },
    { name: 'Minhas Consultas', route: 'portal.agendamentos', icon: 'calendar' },
    { name: 'Meu Perfil', route: 'portal.perfil', icon: 'user' },
];

function isActive(routeName) {
    return route().current(routeName) || route().current(routeName + '.*');
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-emerald-50">
        <!-- Navbar -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-teal-100 shadow-sm sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo + Brand -->
                    <div class="flex items-center gap-3">
                        <Link :href="route('portal.dashboard')" class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <span class="text-lg font-bold bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">
                                Portal do Paciente
                            </span>
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex items-center gap-1">
                        <template v-for="item in navigation" :key="item.route">
                            <Link
                                :href="route(item.route)"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                    isActive(item.route)
                                        ? 'bg-teal-50 text-teal-700 shadow-sm'
                                        : 'text-gray-600 hover:text-teal-700 hover:bg-teal-50/50'
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                        </template>
                    </div>

                    <!-- User Menu -->
                    <div class="hidden sm:flex items-center gap-3">
                        <span class="text-sm text-gray-600">
                            Olá, <strong class="text-teal-700">{{ paciente?.nome?.split(' ')[0] }}</strong>
                        </span>
                        <Link
                            :href="route('portal.logout')"
                            method="post"
                            as="button"
                            class="px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                            Sair
                        </Link>
                    </div>

                    <!-- Mobile Hamburger -->
                    <button
                        @click="showMobileMenu = !showMobileMenu"
                        class="sm:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="showMobileMenu" class="sm:hidden border-t border-teal-100 bg-white/95 backdrop-blur-md">
                    <div class="px-4 py-3 space-y-1">
                        <template v-for="item in navigation" :key="item.route">
                            <Link
                                :href="route(item.route)"
                                :class="[
                                    'block px-4 py-2.5 rounded-lg text-sm font-medium transition-colors',
                                    isActive(item.route)
                                        ? 'bg-teal-50 text-teal-700'
                                        : 'text-gray-600 hover:bg-gray-50'
                                ]"
                            >
                                {{ item.name }}
                            </Link>
                        </template>
                    </div>
                    <div class="border-t border-gray-100 px-4 py-3">
                        <p class="text-sm text-gray-500 mb-2">{{ paciente?.nome }}</p>
                        <Link
                            :href="route('portal.logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                        >
                            Sair
                        </Link>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- Aviso Senha Provisória -->
        <div v-if="paciente?.senha_provisoria" class="bg-amber-500 shadow-inner">
            <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3 text-white">
                        <svg class="h-6 w-6 shrink-0 text-amber-100" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-sm font-medium">
                            Atenção! Você está usando uma senha provisória. Por segurança, recomendamos que altere sua senha agora mesmo.
                        </p>
                    </div>
                    <Link :href="route('portal.perfil')" class="shrink-0 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition">
                        Alterar Senha
                    </Link>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ $page.props.flash.success }}
            </div>
        </div>
        <div v-if="$page.props.flash?.error" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="mt-0">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Content -->
        <main class="pb-12">
            <slot />
        </main>
    </div>
</template>
