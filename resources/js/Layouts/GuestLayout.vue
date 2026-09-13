<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 py-6 sm:py-12"
    >
        <!-- Логотип -->
        <div>
            <Link href="/">
                <ApplicationLogo
                    class="h-20 w-20 fill-current text-amber-500 transition-transform duration-300 hover:scale-105"
                />
            </Link>
        </div>

        <!-- Картка форми (Контейнер) -->
        <div
            class="mt-6 w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/90 px-6 py-8 shadow-2xl backdrop-blur-sm sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
