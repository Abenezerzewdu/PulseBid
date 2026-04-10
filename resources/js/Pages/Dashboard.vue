<script setup>
import { computed } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";
import { defineProps } from "vue";

const props = defineProps({
    user: Object,
    activeAuctions: Number,
    totalBidsPlaced: Number,
    auctionsWon: Number,
    totalSpent: Number,
});
</script>

<template>
    <Head title="Dashboard — PulseBid" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Welcome -->
            <div class="mb-10">
                <p
                    class="text-xs text-white/40 font-semibold tracking-widest mb-2"
                >
                    WELCOME BACK
                </p>
                <h1 class="font-display text-3xl sm:text-4xl font-bold">
                    {{ user.name }}
                </h1>
            </div>

            <!-- Quick stats -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div class="bg-surface-container rounded-3xl p-5">
                    <p class="text-xs text-white/40 mb-1">Active Auctions</p>
                    <p class="font-display text-3xl font-bold text-primary">
                        {{ activeAuctions }}
                    </p>
                </div>
                <div class="bg-surface-container rounded-3xl p-5">
                    <p class="text-xs text-white/40 mb-1">Total Bids Placed</p>
                    <p class="font-display text-3xl font-bold text-secondary">
                        {{ totalBidsPlaced }}
                    </p>
                </div>
                <div class="bg-surface-container rounded-3xl p-5">
                    <p class="text-xs text-white/40 mb-1">Auctions Won</p>
                    <p class="font-display text-3xl font-bold">
                        {{ auctionsWon }}
                    </p>
                </div>
                <div class="bg-surface-container rounded-3xl p-5">
                    <p class="text-xs text-white/40 mb-1">Total Spent</p>
                    <p class="font-display text-3xl font-bold">
                        {{ totalSpent }} ETB
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="grid sm:grid-cols-2 gap-5 mb-10">
                <Link
                    href="/auctions/create"
                    class="group bg-surface-container rounded-3xl p-6 hover:bg-surface-bright transition-all duration-200 hover:-translate-y-1 hover:shadow-ambient"
                >
                    <div
                        class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center mb-4 group-hover:bg-primary/30 transition-colors"
                    >
                        <svg
                            class="w-6 h-6 text-primary"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </div>
                    <h3 class="font-display font-bold text-lg">
                        Launch Auction
                    </h3>
                    <p class="text-sm text-white/40 mt-1">
                        List a new item for bidding
                    </p>
                </Link>
                <Link
                    href="/"
                    class="group bg-surface-container rounded-3xl p-6 hover:bg-surface-bright transition-all duration-200 hover:-translate-y-1 hover:shadow-ambient"
                >
                    <div
                        class="w-12 h-12 rounded-2xl bg-secondary/20 flex items-center justify-center mb-4 group-hover:bg-secondary/30 transition-colors"
                    >
                        <svg
                            class="w-6 h-6 text-secondary"
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
                    <h3 class="font-display font-bold text-lg">
                        Explore Auctions
                    </h3>
                    <p class="text-sm text-white/40 mt-1">
                        Browse live and upcoming items
                    </p>
                </Link>
            </div>

            <!-- Recent activity placeholder -->
            <div class="bg-surface-container rounded-3xl p-6">
                <p class="font-display font-semibold mb-4">Recent Activity</p>
                <div class="text-center py-12 text-white/20">
                    <svg
                        class="w-12 h-12 mx-auto mb-3"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                    </svg>
                    <p class="text-sm">
                        No activity yet. Start bidding or create an auction.
                    </p>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>
