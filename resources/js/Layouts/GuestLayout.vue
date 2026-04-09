<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-surface flex flex-col items-center justify-center px-4 py-12">
        <!-- Logo -->
        <Link href="/" class="font-display font-bold text-2xl text-white mb-8">
            Pulse<span class="text-primary">Bid</span>
        </Link>

        <div class="w-full max-w-md bg-surface-container rounded-3xl p-8 shadow-ambient">
            <slot />
        </div>

        <p class="mt-6 text-xs text-white/20">© 2024 PulseBid Kinetic Marketplace</p>
    </div>
</template>
