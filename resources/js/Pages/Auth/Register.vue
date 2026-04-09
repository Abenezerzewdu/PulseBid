<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register — PulseBid" />

        <h2 class="font-display text-2xl font-bold mb-1">Create account</h2>
        <p class="text-sm text-white/40 mb-6">Join the PulseBid marketplace</p>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-white/50 mb-2">Full Name</label>
                <input
                    v-model="form.name"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Your name"
                    class="pb-input"
                    :class="form.errors.name ? 'ring-2 ring-vivid/50' : ''"
                />
                <p v-if="form.errors.name" class="text-xs text-vivid mt-1">{{ form.errors.name }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/50 mb-2">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
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
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="pb-input"
                    :class="form.errors.password ? 'ring-2 ring-vivid/50' : ''"
                />
                <p v-if="form.errors.password" class="text-xs text-vivid mt-1">{{ form.errors.password }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-white/50 mb-2">Confirm Password</label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="pb-input"
                    :class="form.errors.password_confirmation ? 'ring-2 ring-vivid/50' : ''"
                />
                <p v-if="form.errors.password_confirmation" class="text-xs text-vivid mt-1">{{ form.errors.password_confirmation }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full pb-btn-primary py-3.5 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ form.processing ? 'Creating account...' : 'Create Account' }}
            </button>

            <p class="text-center text-sm text-white/40">
                Already have an account?
                <Link :href="route('login')" class="text-primary hover:underline">Sign in</Link>
            </p>
        </form>
    </GuestLayout>
</template>
