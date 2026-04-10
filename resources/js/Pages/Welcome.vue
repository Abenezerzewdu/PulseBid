<script setup>
import { Head, Link } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const featuredAuctions = [
    {
        id: 1,
        title: "Neon Zenith: Limited 1988 Archive",
        status: "live",
        timeLeft: "02h 11m 56s",
        currentBid: "4.82 ETB",
        image: null,
    },
    {
        id: 2,
        title: "Vitra Lounge: Artisan Edition #04",
        status: "upcoming",
        startsIn: "06h 21m",
        reservePrice: "1.20 ETB",
        image: null,
    },
    {
        id: 3,
        title: "Neural Linker: Modular Core Prototype",
        status: "live",
        timeLeft: "44m 10s",
        currentBid: "22.10 ETB",
        image: null,
    },
];
</script>

<template>
    <Head title="PulseBid — Abz" />
    <PulseBidLayout>
        <!-- Hero -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in">
                    <h1
                        class="font-display text-5xl sm:text-6xl lg:text-7xl font-bold leading-tight"
                    >
                        Discover
                        <span class="text-primary italic"> Live</span><br />
                        Auctions
                    </h1>
                    <p
                        class="mt-6 text-white/50 text-lg max-w-md leading-relaxed"
                    >
                        Experience the velocity of real-time bidding on curated
                        digital assets and physical masterpieces. Where the
                        digital curator meets the modern collector.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <Link
                            href="/auctions"
                            class="pb-btn-primary text-base px-8 py-3.5"
                        >
                            Start Bidding
                        </Link>
                        <button class="pb-btn-secondary text-base px-8 py-3.5">
                            How it works
                        </button>
                    </div>
                </div>

                <!-- Hero card -->
                <div class="relative">
                    <div
                        class="bg-surface-container rounded-3xl overflow-hidden aspect-[4/3] flex items-center justify-center relative"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-secondary/10"
                        ></div>
                        <div class="text-center text-white/20">
                            <svg
                                class="w-24 h-24 mx-auto"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                            <p class="mt-2 text-sm">Featured Item</p>
                        </div>
                        <!-- Bid badge -->
                        <div
                            class="absolute bottom-4 right-4 bg-surface-container/80 backdrop-blur-xl rounded-2xl px-4 py-3 border border-white/10"
                        >
                            <p class="text-xs text-white/40 font-display">
                                CURRENT BID
                            </p>
                            <p
                                class="text-xl font-display font-bold text-primary"
                            >
                                12.45 ETB
                            </p>
                        </div>
                        <!-- Live indicator -->
                        <div
                            class="absolute top-4 left-4 flex items-center gap-2 bg-surface-container/80 backdrop-blur-xl rounded-full px-3 py-1.5 border border-white/10"
                        >
                            <span class="pulse-dot"></span>
                            <span class="text-xs font-medium text-secondary"
                                >LIVE</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Live Now section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="font-display text-2xl font-bold">Live Now</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="pulse-dot"></span>
                        <span class="text-xs text-white/40"
                            >1,240 PEOPLE BIDDING</span
                        >
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        class="w-9 h-9 rounded-full bg-surface-container hover:bg-surface-bright flex items-center justify-center transition-colors"
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
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </button>
                    <button
                        class="w-9 h-9 rounded-full bg-surface-container hover:bg-surface-bright flex items-center justify-center transition-colors"
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
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="item in featuredAuctions"
                    :key="item.id"
                    class="bg-surface-container rounded-3xl overflow-hidden group hover:shadow-ambient transition-all duration-300 hover:-translate-y-1"
                >
                    <!-- Image -->
                    <div
                        class="aspect-[4/3] bg-surface-bright relative flex items-center justify-center"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5"
                        ></div>
                        <svg
                            class="w-16 h-16 text-white/10"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        <!-- Status badge -->
                        <div class="absolute top-3 left-3">
                            <span
                                v-if="item.status === 'live'"
                                class="flex items-center gap-1.5 bg-primary/20 text-primary text-xs font-semibold px-2.5 py-1 rounded-full"
                            >
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"
                                ></span>
                                LIVE
                            </span>
                            <span
                                v-else
                                class="bg-surface-bright text-white/60 text-xs font-semibold px-2.5 py-1 rounded-full"
                            >
                                STARTING SOON
                            </span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <h3
                                class="font-display font-semibold text-sm leading-snug"
                            >
                                {{ item.title }}
                            </h3>
                            <div class="text-right shrink-0">
                                <p class="text-xs text-white/40">
                                    {{
                                        item.status === "live"
                                            ? "TIME LEFT"
                                            : "STARTS IN"
                                    }}
                                </p>
                                <p
                                    class="text-xs font-display font-bold text-vivid"
                                >
                                    {{ item.timeLeft || item.startsIn }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-white/40">
                                    {{
                                        item.status === "live"
                                            ? "CURRENT BID"
                                            : "RESERVE PRICE"
                                    }}
                                </p>
                                <p class="font-display font-bold text-white">
                                    {{ item.currentBid || item.reservePrice }}
                                </p>
                            </div>
                            <Link
                                :href="`/auctions/${item.id}`"
                                class="pb-btn-primary text-xs py-2 px-4"
                            >
                                {{
                                    item.status === "live"
                                        ? "Place Bid"
                                        : "Set Reminder"
                                }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
            <div class="grid md:grid-cols-3 gap-5">
                <div class="bg-surface-container rounded-3xl p-8 md:col-span-1">
                    <p
                        class="font-display text-2xl font-bold text-primary leading-tight"
                    >
                        PulseBid is the market leader for rare finds.
                    </p>
                    <div class="mt-6 flex gap-8">
                        <div>
                            <p class="font-display text-2xl font-bold">14.2k</p>
                            <p class="text-xs text-white/40 mt-1">
                                Active Sellers
                            </p>
                        </div>
                        <div>
                            <p class="font-display text-2xl font-bold">$2.4B</p>
                            <p class="text-xs text-white/40 mt-1">
                                Auction Volume
                            </p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-primary rounded-3xl p-8 flex flex-col items-center justify-center text-surface-lowest"
                >
                    <svg
                        class="w-10 h-10 mb-3"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <p class="font-display font-bold text-lg text-center">
                        100% Verified Authentication
                    </p>
                </div>
                <div class="bg-surface-container rounded-3xl p-8">
                    <p
                        class="text-xs text-white/40 font-semibold tracking-widest mb-3"
                    >
                        WEEKLY SPOTLIGHT
                    </p>
                    <h3 class="font-display font-bold text-xl">
                        The 1968 Chronos Heritage Collection
                    </h3>
                    <button
                        class="mt-4 w-9 h-9 rounded-full bg-surface-bright flex items-center justify-center hover:bg-primary hover:text-surface-lowest transition-colors"
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
                                d="M9 5l7 7-7 7"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    </PulseBidLayout>
</template>
