<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";

const props = defineProps({
    stats: Object,
    health: Object,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "ETB",
    }).format(val);
};

const healthColors = {
    Connected: "text-primary bg-primary/10 border-primary/20",
    Online: "text-primary bg-primary/10 border-primary/20",
    Offline: "text-vivid bg-vivid/10 border-vivid/20",
    Error: "text-vivid bg-vivid/10 border-vivid/20",
    local: "text-secondary bg-secondary/10 border-secondary/20",
    production: "text-primary bg-primary/10 border-primary/20",
};

const getHealthColor = (val) => {
    if (val?.startsWith("Error")) return healthColors["Error"];
    return healthColors[val] || "text-white/40 bg-white/5 border-white/10";
};
</script>

<template>
    <Head title="Admin Dashboard — PulseBid" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Header -->
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10"
            >
                <div>
                    <h1 class="font-display text-4xl font-bold tracking-tight">
                        System Control
                    </h1>
                    <p class="mt-2 text-white/50">
                        Oversee marketplace health, statistics, and core
                        infrastructure.
                    </p>
                </div>
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-surface-container rounded-2xl border border-white/5"
                >
                    <span
                        class="w-2 h-2 rounded-full bg-primary animate-pulse"
                    ></span>
                    <span
                        class="text-xs font-semibold text-white/70 uppercase tracking-widest"
                        >Admin Authorization Active</span
                    >
                </div>
            </div>

            <!-- Stats Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12"
            >
                <div class="pb-card relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"
                    ></div>
                    <p
                        class="text-xs font-bold text-white/40 tracking-widest uppercase mb-4"
                    >
                        Total Ecosystem Users
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-4xl font-display font-bold">
                            {{ stats.total_users }}
                        </p>
                        <div
                            class="w-10 h-10 rounded-xl bg-surface-bright flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-primary"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="pb-card relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-secondary/5 rounded-full blur-2xl group-hover:bg-secondary/10 transition-colors"
                    ></div>
                    <p
                        class="text-xs font-bold text-white/40 tracking-widest uppercase mb-4"
                    >
                        Live Auctions
                    </p>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-4xl font-display font-bold">
                                {{ stats.live_auctions }}
                            </p>
                            <p
                                class="text-[10px] text-white/30 mt-1 uppercase font-bold tracking-tighter"
                            >
                                Of {{ stats.total_auctions }} Global
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-surface-bright flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-secondary"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="pb-card relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-vivid/5 rounded-full blur-2xl group-hover:bg-vivid/10 transition-colors"
                    ></div>
                    <p
                        class="text-xs font-bold text-white/40 tracking-widest uppercase mb-4"
                    >
                        Total Bids Placed
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-4xl font-display font-bold">
                            {{ stats.total_bids }}
                        </p>
                        <div
                            class="w-10 h-10 rounded-xl bg-surface-bright flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-vivid"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="pb-card relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full blur-2xl group-hover:bg-primary/10 transition-colors"
                    ></div>
                    <p
                        class="text-xs font-bold text-white/40 tracking-widest uppercase mb-4"
                    >
                        Total Revenue
                    </p>
                    <div class="flex items-end justify-between">
                        <p class="text-4xl font-display font-bold">
                            {{ formatCurrency(stats.total_revenue) }}
                        </p>
                        <div
                            class="w-10 h-10 rounded-xl bg-surface-bright flex items-center justify-center"
                        >
                            <svg
                                class="w-5 h-5 text-primary"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-[1fr_400px] gap-8">
                <!-- System Health Console -->
                <div class="space-y-6">
                    <div class="pb-card border border-white/5">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="font-display text-xl font-bold">
                                Infrastucture Diagnostics
                            </h2>
                            <button
                                class="text-xs font-semibold text-primary hover:underline transition-all"
                            >
                                Refresh Status
                            </button>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div
                                class="p-4 rounded-2xl bg-surface-lowest border border-white/5 flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-surface-bright flex items-center justify-center text-white/40"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-white/70"
                                        >Database Engine</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-bold px-2 py-1 rounded-md border"
                                    :class="getHealthColor(health.database)"
                                    >{{ health.database }}</span
                                >
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-surface-lowest border border-white/5 flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-surface-bright flex items-center justify-center text-white/40"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8.111 16.404a5.5 5.5 0 117.778 0M12 20h.01m-7.08-7.071a9.05 9.05 0 0112.728 0M6.343 17.657l1.414-1.414m8.486 0l1.414 1.414"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-white/70"
                                        >Reverb WebSocket</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-bold px-2 py-1 rounded-md border"
                                    :class="getHealthColor(health.reverb)"
                                    >{{ health.reverb }}</span
                                >
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-surface-lowest border border-white/5 flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-surface-bright flex items-center justify-center text-white/40"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-white/70"
                                        >Environment</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-bold px-2 py-1 rounded-md border"
                                    :class="getHealthColor(health.environment)"
                                    >{{
                                        health.environment?.toUpperCase()
                                    }}</span
                                >
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-surface-lowest border border-white/5 flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-surface-bright flex items-center justify-center text-white/40"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm font-medium text-white/70"
                                        >PHP Version</span
                                    >
                                </div>
                                <span
                                    class="text-[10px] font-bold text-white/50 px-2 py-1 rounded-md border border-white/10 uppercase"
                                    >{{ health.php_version }}</span
                                >
                            </div>
                        </div>

                        <!-- System Log Mockup -->
                        <div class="mt-8">
                            <p
                                class="text-[10px] font-bold text-white/20 uppercase tracking-widest mb-4"
                            >
                                Latest Server Pulse
                            </p>
                            <div
                                class="bg-surface-lowest rounded-2xl p-4 font-mono text-[11px] text-white/40 border border-white/5 leading-relaxed"
                            >
                                <div class="flex gap-4">
                                    <span class="text-secondary"
                                        >[{{ health.server_time }}]</span
                                    >
                                    <span
                                        >INFO: Marketplace engine initialized
                                        successfully.</span
                                    >
                                </div>
                                <div class="flex gap-4">
                                    <span class="text-secondary"
                                        >[{{ health.server_time }}]</span
                                    >
                                    <span
                                        >BROADCAST: Reverb engine listening on
                                        port 8008.</span
                                    >
                                </div>
                                <div class="flex gap-4">
                                    <span class="text-secondary"
                                        >[{{ health.server_time }}]</span
                                    >
                                    <span
                                        >CACHE: Configuration manifest loaded
                                        (256ms).</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Capabilities Section -->
                <div class="space-y-6">
                    <div
                        class="pb-card bg-surface-container border border-white/5"
                    >
                        <h3 class="font-display font-bold mb-6">
                            High-Level Controls
                        </h3>
                        <div class="space-y-4">
                            <button
                                class="w-full flex items-center justify-between p-4 rounded-2xl bg-surface-bright hover:bg-white/10 transition-colors group"
                            >
                                <div class="flex items-center gap-3 text-left">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors"
                                    >
                                        <svg
                                            class="w-5 h-5 text-primary"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">
                                            User Moderation
                                        </p>
                                        <p class="text-[10px] text-white/30">
                                            Suspend or verify accounts
                                        </p>
                                    </div>
                                </div>
                                <svg
                                    class="w-4 h-4 text-white/20"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </button>

                            <button
                                class="w-full flex items-center justify-between p-4 rounded-2xl bg-surface-bright hover:bg-white/10 transition-colors group"
                            >
                                <div class="flex items-center gap-3 text-left">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors"
                                    >
                                        <svg
                                            class="w-5 h-5 text-secondary"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">
                                            Auction Management
                                        </p>
                                        <p class="text-[10px] text-white/30">
                                            Review or flag suspicious items
                                        </p>
                                    </div>
                                </div>
                                <svg
                                    class="w-4 h-4 text-white/20"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </button>

                            <button
                                class="w-full flex items-center justify-between p-4 rounded-2xl bg-surface-bright/50 cursor-not-allowed opacity-50 transition-colors group"
                            >
                                <div class="flex items-center gap-3 text-left">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white/40"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold">
                                            Global Maintenance Mode
                                        </p>
                                        <p class="text-[10px] text-white/30">
                                            Locked — Requires MFA
                                        </p>
                                    </div>
                                </div>
                                <svg
                                    class="w-4 h-4 text-white/20"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pb-card bg-primary/5 border border-primary/20">
                        <h4
                            class="text-xs font-bold text-primary uppercase tracking-widest mb-2"
                        >
                            Pro Tip: System Optimization
                        </h4>
                        <p class="text-[11px] text-white/50 leading-relaxed">
                            Monitor the
                            <span class="text-primary font-semibold"
                                >Reverb WebSocket</span
                            >
                            status during high-traffic auction endings. If it
                            goes transition to
                            <span class="text-vivid font-semibold">Offline</span
                            >, existing bidders may experience sync delays.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>

<style scoped>
.pb-card {
    @apply bg-surface-container rounded-3xl p-6 transition-all duration-300;
}
</style>
