<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth?.user);
const mobileOpen = ref(false);

const navLinks = [
    { label: "Explore", href: "/" },
    { label: "Live", href: "/auctions?filter=live" },
    { label: "Upcoming", href: "/auctions?filter=upcoming" },
];
</script>

<template>
    <div class="min-h-screen bg-surface text-white">
        <!-- Navbar -->
        <nav
            class="sticky top-0 z-50 bg-surface/80 backdrop-blur-xl border-b border-white/5"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link
                        href="/"
                        class="font-display font-bold text-xl tracking-tight text-white"
                    >
                        Pulse<span class="text-primary">Bid</span>
                    </Link>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-8">
                        <Link
                            v-for="link in navLinks"
                            :key="link.label"
                            :href="link.href"
                            class="text-sm font-medium text-white/60 hover:text-white transition-colors duration-200 relative group"
                        >
                            {{ link.label }}
                            <span
                                class="absolute -bottom-1 left-0 w-0 h-0.5 bg-primary group-hover:w-full transition-all duration-300 rounded-full"
                            ></span>
                        </Link>
                    </div>

                    <!-- Right side -->
                    <div class="hidden md:flex items-center gap-3">
                        <!-- Search -->
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search auctions..."
                                class="bg-surface-container text-sm text-white/70 placeholder-white/30 rounded-full px-4 py-2 pl-9 border-0 focus:ring-1 focus:ring-primary/40 w-48 transition-all duration-200 focus:w-64"
                            />
                            <svg
                                class="absolute left-3 top-2.5 w-4 h-4 text-white/30"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>

                        <!-- Notifications -->
                        <button
                            class="relative w-9 h-9 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-bright transition-colors"
                        >
                            <svg
                                class="w-4 h-4 text-white/70"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                />
                            </svg>
                            <span
                                class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-vivid rounded-full"
                            ></span>
                        </button>

                        <template v-if="user">
                            <Link
                                href="/auctions/create"
                                class="pb-btn-primary text-sm py-2 px-4"
                            >
                                Create Auction
                            </Link>
                            <!-- Avatar dropdown -->
                            <div class="relative group">
                                <button
                                    class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-surface-lowest font-bold text-sm"
                                >
                                    {{ user.name?.charAt(0).toUpperCase() }}
                                </button>
                                <div
                                    class="absolute right-0 top-full mt-2 w-48 bg-surface-container rounded-2xl shadow-ambient border border-white/5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-2"
                                >
                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-surface-bright transition-colors"
                                        >Profile</Link
                                    >
                                    <Link
                                        :href="route('dashboard')"
                                        class="block px-4 py-2 text-sm text-white/70 hover:text-white hover:bg-surface-bright transition-colors"
                                        >Dashboard</Link
                                    >
                                    <hr class="border-white/10 my-1" />
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="block w-full text-left px-4 py-2 text-sm text-white/70 hover:text-vivid hover:bg-surface-bright transition-colors"
                                        >Log Out</Link
                                    >
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="text-sm text-white/70 hover:text-white transition-colors"
                                >Log in</Link
                            >
                            <Link
                                :href="route('register')"
                                class="pb-btn-primary text-sm py-2 px-4"
                                >Register</Link
                            >
                        </template>
                    </div>

                    <!-- Mobile hamburger -->
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden w-9 h-9 flex items-center justify-center rounded-full bg-surface-container"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                v-if="!mobileOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div
                v-if="mobileOpen"
                class="md:hidden bg-surface-container border-t border-white/5 px-4 py-4 space-y-3 animate-fade-in"
            >
                <Link
                    v-for="link in navLinks"
                    :key="link.label"
                    :href="link.href"
                    class="block text-sm text-white/70 hover:text-white py-2"
                    >{{ link.label }}</Link
                >
                <hr class="border-white/10" />
                <template v-if="user">
                    <Link
                        href="/auctions/create"
                        class="block pb-btn-primary text-center text-sm py-2"
                        >Create Auction</Link
                    >
                    <Link
                        :href="route('profile.edit')"
                        class="block text-sm text-white/70 py-2"
                        >Profile</Link
                    >
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block text-sm text-white/70 py-2"
                        >Log Out</Link
                    >
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="block text-sm text-white/70 py-2"
                        >Log in</Link
                    >
                    <Link
                        :href="route('register')"
                        class="block pb-btn-primary text-center text-sm py-2"
                        >Register</Link
                    >
                </template>
            </div>
        </nav>

        <!-- Page content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-white/5 mt-24 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col md:flex-row items-center justify-between gap-6"
                >
                    <div>
                        <p class="font-display font-bold text-lg text-white">
                            Pulse<span class="text-primary">Bid</span>
                        </p>
                        <p class="text-xs text-white/30 mt-1">
                            © 2026 PulseBid By AbenezerZ
                        </p>
                    </div>
                    <div class="flex items-center gap-6 text-xs text-white/40">
                        <a href="#" class="hover:text-white transition-colors"
                            >Terms</a
                        >
                        <a href="#" class="hover:text-white transition-colors"
                            >Privacy</a
                        >
                        <a href="#" class="hover:text-white transition-colors"
                            >Support</a
                        >
                        <a href="#" class="hover:text-white transition-colors"
                            >API</a
                        >
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
