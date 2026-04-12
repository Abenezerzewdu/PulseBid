<script setup>
import { ref, computed } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";
import LiveStatus from "@/Components/LiveStatus.vue";
import StartingSoon from "@/Components/StartingSoon.vue";

const props = defineProps({
    auctions: Array,
    filters: Object,
});

// ─── Local state ───────────────────────────────────────────────
const search = ref(props.filters?.search ?? "");
const activeFilter = ref(props.filters?.status ?? "all");
const activeSort = ref(props.filters?.sort ?? "newest");
const viewMode = ref("grid"); // "grid" | "list"

const statusTabs = [
    { key: "all",      label: "All" },
    { key: "live",     label: "Live Now" },
    { key: "upcoming", label: "Upcoming" },
    { key: "ended",    label: "Ended" },
];

const sortOptions = [
    { key: "newest",    label: "Newest" },
    { key: "ending",    label: "Ending Soon" },
    { key: "price_asc", label: "Price: Low → High" },
    { key: "price_desc",label: "Price: High → Low" },
];

// ─── Helpers ───────────────────────────────────────────────────
function applyFilters() {
    router.get(
        "/auctions",
        {
            search: search.value || undefined,
            status: activeFilter.value !== "all" ? activeFilter.value : undefined,
            sort: activeSort.value !== "newest" ? activeSort.value : undefined,
        },
        { preserveState: true, replace: true }
    );
}

function setStatus(key) {
    activeFilter.value = key;
    applyFilters();
}

function setSort(key) {
    activeSort.value = key;
    applyFilters();
}

let searchTimeout = null;
function onSearchInput() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 350);
}

function formatPrice(val) {
    if (val == null) return "—";
    return Number(val).toLocaleString("en-ET") + " ETB";
}

function timeLabel(auction) {
    if (auction.status === "live") return "TIME LEFT";
    if (auction.status === "upcoming") return "STARTS IN";
    return "ENDED";
}

function timeValue(auction) {
    return auction.timeLeft ?? auction.startsIn ?? "–";
}

function bidLabel(auction) {
    if (auction.status === "live") return "CURRENT BID";
    if (auction.status === "upcoming") return "RESERVE PRICE";
    return "FINAL BID";
}

function bidValue(auction) {
    return formatPrice(auction.current_price ?? auction.starting_price);
}

function actionLabel(auction) {
    if (auction.status === "live") return "Place Bid";
    if (auction.status === "upcoming") return "View Details";
    return "View Results";
}

function statusColor(status) {
    if (status === "live") return "text-primary";
    if (status === "upcoming") return "text-secondary";
    return "text-white/30";
}
</script>

<template>
    <Head title="Auctions — PulseBid" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <!-- ── Page header ──────────────────────────────────── -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-xs text-white/30 font-semibold tracking-widest mb-1">MARKETPLACE</p>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold leading-tight">
                        Explore <span class="text-primary">Auctions</span>
                    </h1>
                </div>
                <Link
                    href="/auctions/create"
                    class="pb-btn-primary shrink-0 self-start sm:self-auto text-sm py-2.5 px-5"
                >
                    + Create Auction
                </Link>
            </div>

            <!-- ── Search + Controls bar ────────────────────────── -->
            <div class="flex flex-col md:flex-row md:items-center gap-3 mb-6">

                <!-- Search -->
                <div class="relative flex-1 max-w-sm">
                    <svg
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30 pointer-events-none"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        @input="onSearchInput"
                        type="text"
                        placeholder="Search auctions…"
                        class="w-full bg-surface-container border-0 rounded-2xl text-sm text-white/80 placeholder-white/20 py-2.5 pl-10 pr-4 focus:ring-1 focus:ring-primary/40 transition-all"
                    />
                </div>

                <!-- Sort -->
                <select
                    :value="activeSort"
                    @change="setSort($event.target.value)"
                    class="bg-surface-container border-0 text-sm text-white/70 rounded-2xl px-4 py-2.5 focus:ring-1 focus:ring-primary/40 cursor-pointer"
                >
                    <option v-for="s in sortOptions" :key="s.key" :value="s.key">{{ s.label }}</option>
                </select>

                <!-- View toggle -->
                <div class="flex items-center gap-1 bg-surface-container rounded-2xl p-1 shrink-0">
                    <button
                        @click="viewMode = 'grid'"
                        :class="[
                            'w-8 h-8 flex items-center justify-center rounded-xl transition-colors',
                            viewMode === 'grid' ? 'bg-surface-bright text-white' : 'text-white/30 hover:text-white/60'
                        ]"
                        title="Grid view"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3A1.5 1.5 0 0 1 15 10.5v3A1.5 1.5 0 0 1 13.5 15h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                        </svg>
                    </button>
                    <button
                        @click="viewMode = 'list'"
                        :class="[
                            'w-8 h-8 flex items-center justify-center rounded-xl transition-colors',
                            viewMode === 'list' ? 'bg-surface-bright text-white' : 'text-white/30 hover:text-white/60'
                        ]"
                        title="List view"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ── Status filter tabs ───────────────────────────── -->
            <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-1 scrollbar-hide">
                <button
                    v-for="tab in statusTabs"
                    :key="tab.key"
                    @click="setStatus(tab.key)"
                    :class="[
                        'shrink-0 text-sm font-medium px-4 py-1.5 rounded-full border transition-all duration-200',
                        activeFilter === tab.key
                            ? 'bg-primary text-surface-lowest border-primary'
                            : 'border-white/10 text-white/50 hover:text-white hover:border-white/20'
                    ]"
                >
                    <span v-if="tab.key === 'live'" class="inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse inline-block"></span>
                        {{ tab.label }}
                    </span>
                    <span v-else>{{ tab.label }}</span>
                </button>
            </div>

            <!-- ── Results count ────────────────────────────────── -->
            <p class="text-xs text-white/30 mb-5">
                {{ auctions?.length ?? 0 }} auction{{ (auctions?.length ?? 0) !== 1 ? 's' : '' }} found
            </p>

            <!-- ── GRID VIEW ────────────────────────────────────── -->
            <div
                v-if="viewMode === 'grid'"
                class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5"
            >
                <Link
                    v-for="auction in auctions"
                    :key="auction.id"
                    :href="`/auctions/${auction.slug}`"
                    class="group bg-surface-container rounded-3xl overflow-hidden hover:shadow-ambient transition-all duration-300 hover:-translate-y-1 block"
                >
                    <!-- Image area -->
                    <div class="aspect-[4/3] bg-surface-bright relative flex items-center justify-center overflow-hidden">
                        <!-- gradient overlay -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5"></div>

                        <img
                            v-if="auction.image"
                            :src="`/storage/${auction.image}`"
                            :alt="auction.title"
                            class="absolute inset-0 w-full h-full object-cover"
                        />

                        <!-- Placeholder icon -->
                        <svg
                            v-else
                            class="w-14 h-14 text-white/10 relative z-10"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>

                        <!-- Status badge -->
                        <LiveStatus v-if="auction.status === 'live'" />
                        <StartingSoon v-else-if="auction.status === 'upcoming'" />

                        <!-- Ended overlay tint -->
                        <div
                            v-if="auction.status === 'ended'"
                            class="absolute inset-0 bg-surface-lowest/50 flex items-center justify-center"
                        >
                            <span class="text-xs font-semibold text-white/30 tracking-widest">ENDED</span>
                        </div>
                    </div>

                    <!-- Card body -->
                    <div class="p-4">
                        <h3 class="font-display font-semibold text-sm leading-snug line-clamp-2 group-hover:text-primary transition-colors">
                            {{ auction.title }}
                        </h3>

                        <!-- Bid + time row -->
                        <div class="mt-3 flex items-end justify-between gap-2">
                            <div>
                                <p class="text-xs text-white/30">{{ bidLabel(auction) }}</p>
                                <p class="font-display font-bold text-base text-white">
                                    {{ bidValue(auction) }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs text-white/30">{{ timeLabel(auction) }}</p>
                                <p :class="['text-xs font-display font-bold', statusColor(auction.status)]">
                                    {{ timeValue(auction) }}
                                </p>
                            </div>
                        </div>

                        <!-- CTA button -->
                        <button
                            :class="[
                                'mt-4 w-full text-xs font-semibold py-2 rounded-xl transition-all duration-200',
                                auction.status === 'live'
                                    ? 'bg-primary text-surface-lowest hover:bg-primary-container'
                                    : auction.status === 'upcoming'
                                        ? 'bg-secondary/20 text-secondary hover:bg-secondary/30'
                                        : 'bg-surface-bright text-white/40 cursor-default'
                            ]"
                        >
                            {{ actionLabel(auction) }}
                        </button>
                    </div>
                </Link>
            </div>

            <!-- ── LIST VIEW ────────────────────────────────────── -->
            <div v-else class="flex flex-col gap-3">
                <Link
                    v-for="auction in auctions"
                    :key="auction.id"
                    :href="`/auctions/${auction.slug}`"
                    class="group bg-surface-container rounded-2xl p-4 flex items-center gap-4 hover:bg-surface-bright transition-all duration-200 hover:-translate-x-0.5 hover:shadow-ambient"
                >
                    <!-- Thumbnail -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-surface-bright shrink-0 overflow-hidden relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5"></div>
                        <img
                            v-if="auction.image"
                            :src="`/storage/${auction.image}`"
                            :alt="auction.title"
                            class="absolute inset-0 w-full h-full object-cover"
                        />
                        <svg
                            v-else
                            class="w-6 h-6 text-white/10 relative z-10"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span
                                v-if="auction.status === 'live'"
                                class="inline-flex items-center gap-1 text-xs text-primary font-semibold"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                LIVE
                            </span>
                            <span
                                v-else-if="auction.status === 'upcoming'"
                                class="text-xs text-secondary font-semibold"
                            >UPCOMING</span>
                            <span v-else class="text-xs text-white/30 font-semibold">ENDED</span>
                        </div>
                        <h3 class="font-display font-semibold text-sm truncate group-hover:text-primary transition-colors">
                            {{ auction.title }}
                        </h3>
                        <p class="text-xs text-white/30 mt-0.5 truncate">by {{ auction.user?.name ?? 'Unknown Seller' }}</p>
                    </div>

                    <!-- Price -->
                    <div class="text-right shrink-0 hidden sm:block">
                        <p class="text-xs text-white/30">{{ bidLabel(auction) }}</p>
                        <p class="font-display font-bold text-sm">{{ bidValue(auction) }}</p>
                    </div>

                    <!-- Time -->
                    <div class="text-right shrink-0 hidden md:block">
                        <p class="text-xs text-white/30">{{ timeLabel(auction) }}</p>
                        <p :class="['text-xs font-display font-bold', statusColor(auction.status)]">
                            {{ timeValue(auction) }}
                        </p>
                    </div>

                    <!-- Arrow -->
                    <svg class="w-4 h-4 text-white/20 group-hover:text-primary shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

            <!-- ── Empty state ──────────────────────────────────── -->
            <div
                v-if="!auctions || auctions.length === 0"
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <div class="w-20 h-20 rounded-3xl bg-surface-container flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-white/10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h2 class="font-display font-bold text-xl mb-2">No auctions found</h2>
                <p class="text-sm text-white/30 max-w-xs">
                    {{ search ? 'Try a different search term or clear your filters.' : 'Be the first to list an item for auction.' }}
                </p>
                <div class="flex gap-3 mt-6">
                    <button
                        v-if="search || activeFilter !== 'all'"
                        @click="search = ''; setStatus('all')"
                        class="pb-btn-secondary text-sm py-2 px-5"
                    >Clear Filters</button>
                    <Link href="/auctions/create" class="pb-btn-primary text-sm py-2 px-5">
                        Create Auction
                    </Link>
                </div>
            </div>

        </div>
    </PulseBidLayout>
</template>

<style scoped>
/* Hide scrollbar on filter tabs on mobile */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
