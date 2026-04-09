<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in — PulseBid" />

        <h2 class="font-display text-2xl font-bold mb-1">Welcome back</h2>
        <p class="text-sm text-white/40 mb-6">Sign in to your PulseBid account</p>

        <div v-if="status" class="mb-4 text-sm text-primary bg-primary/10 rounded-2xl px-4 py-3">{{ status }}</div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-white/50 mb-2">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="pb-input"
                    :class="form.errors.email ? 'ring-2 ring-vivid/50' : ''"
                />
                <p v-if="form.errors.email" class="text-xs text-vivid mt-1">{{ form.errors.email }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/50 mb-2">Password</label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="pb-input"
                    :class="form.errors.password ? 'ring-2 ring-vivid/50' : ''"
                />
                <p v-if="form.errors.password" class="text-xs text-vivid mt-1">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="form.remember" class="rounded bg-surface-bright border-0 text-primary focus:ring-primary/40"/>
                    <span class="text-sm text-white/50">Remember me</span>
                </label>
                <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-white/40 hover:text-primary transition-colors">
                    Forgot password?
                </Link>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full pb-btn-primary py-3.5 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ form.processing ? 'Signing in...' : 'Sign In' }}
            </button>

            <p class="text-center text-sm text-white/40">
                Don't have an account?
                <Link :href="route('register')" class="text-primary hover:underline">Register</Link>
            </p>
        </form>
    </GuestLayout>
</template>
