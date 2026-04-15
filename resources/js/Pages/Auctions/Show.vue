<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { router, usePage, Head, Link } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";

const props = defineProps({
    auction: Object,
    authUserId: Number,
});

const page = usePage();
const errors = computed(() => page.props.errors || {});
const flash  = computed(() => page.props.flash  || {});

const bidAmount   = ref("");
const isSubmitting = ref(false);
// Pre-seed from server flash (covers redirect-then-land scenario)
const successMsg = ref(flash.value?.success ?? "");
const countdown = ref({ hours: 0, mins: 0, secs: 0, ended: false });
const countdownActionType = ref('upcoming');
let timer = null;
let successTimer = null;

// Helper to safely parse dates assuming UTC if no offset given
const parseDateObj = (ds) => {
    if (!ds) return new Date();
    let d = ds.replace(' ', 'T');
    if (!d.includes('Z') && !d.match(/[-+]\d{2}:\d{2}$/)) d += 'Z';
    return new Date(d);
};

// Pick up flash that arrives after an Inertia visit (e.g. post-bid redirect)
watch(() => flash.value?.success, (val) => {
    if (val) {
        successMsg.value = val;
        clearTimeout(successTimer);
        successTimer = setTimeout(() => { successMsg.value = ""; }, 5000);
    }
}, { immediate: true });

const isSeller = computed(() => props.authUserId === props.auction.user_id);
const isLoggedIn = computed(() => !!props.authUserId);

const currentPrice = computed(
    () => props.auction.current_price ?? props.auction.starting_price,
);

const isLive = computed(() => {
    const now = new Date();
    const start = parseDateObj(props.auction.start_time);
    const end = parseDateObj(props.auction.end_time);
    return now >= start && now < end;
});

const updateCountdown = () => {
    const now = new Date();
    const start = parseDateObj(props.auction.start_time);
    const end = parseDateObj(props.auction.end_time);

    let diff = 0;
    
    if (now < start) {
        countdownActionType.value = 'upcoming';
        diff = Math.floor((start - now) / 1000);
    } else if (now >= start && now < end) {
        countdownActionType.value = 'live';
        diff = Math.floor((end - now) / 1000);
    } else {
        countdownActionType.value = 'ended';
        countdown.value = { hours: 0, mins: 0, secs: 0, ended: true };
        clearInterval(timer);
        return;
    }

    countdown.value = {
        hours: Math.floor(diff / 3600),
        mins: Math.floor((diff % 3600) / 60),
        secs: diff % 60,
        ended: false,
    };
};

const pad = (n) => String(n).padStart(2, "0");

const quickBidAmounts = computed(() => {
    const base = parseFloat(currentPrice.value) || 0;
    return [
        { label: "+$100", value: base + 100 },
        { label: "+$500", value: base + 500 },
        { label: "+$1000", value: base + 1000 },
    ];
});

const setQuickBid = (val) => {
    bidAmount.value = val;
};

const placeInstantBid = (val) => {
    bidAmount.value = val;
    placeBid();
};

const placeBid = () => {
    if (!bidAmount.value) return;
    isSubmitting.value = true;
    successMsg.value = "";
    router.post(
        `/auctions/${props.auction.slug}/bid`,
        { amount: bidAmount.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                successMsg.value = flash.value?.success || "Bid placed successfully!";
                bidAmount.value = "";
                setTimeout(() => { successMsg.value = ""; }, 4000);
            },
            onError: () => {
                // errors are reactively available via page.props.errors
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
};

const isHighestBidder = computed(() => {
    if (!props.authUserId || !props.auction.bids?.length) return false;
    const topBid = [...props.auction.bids].sort(
        (a, b) => b.amount - a.amount,
    )[0];
    return topBid?.user_id === props.authUserId;
});

onMounted(() => {
    updateCountdown();
    timer = setInterval(updateCountdown, 1000);

    // Real-time bidirectional listeners for auctions
    window.Echo.channel(`auction.${props.auction.id}`)
        .listen('BidPlaced', (e) => {
            if (!props.auction.bids) props.auction.bids = [];
            // Only add if it doesn't already exist (in case local inertia onSuccess updated it first)
            if (!props.auction.bids.find(b => b.id === e.bid.id)) {
                props.auction.bids.push(e.bid);
            }
            props.auction.current_price = e.bid.amount;
        });
});
onUnmounted(() => {
    clearInterval(timer);
    clearTimeout(successTimer);
    window.Echo.leaveChannel(`auction.${props.auction.id}`);
});
</script>

<template>
    <Head :title="`${auction.title} — PulseBid`" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs text-white/30 mb-6">
                <Link href="/" class="hover:text-white transition-colors"
                    >Marketplace</Link
                >
                <span>/</span>
                <span class="text-white/50">Live Auction</span>
            </div>

            <!-- Title row -->
            <div
                class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8"
            >
                <div>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold">
                        {{ auction.title }}
                    </h1>
                    <p
                        v-if="auction.description"
                        class="mt-2 text-white/50 max-w-xl text-sm leading-relaxed"
                    >
                        {{ auction.description }}
                    </p>
                </div>
                <div
                    v-if="isLive && !countdown.ended"
                    class="flex items-center gap-2 shrink-0"
                >
                    <span class="pulse-dot"></span>
                    <span
                        class="text-xs font-semibold text-secondary tracking-widest"
                        >LIVE BIDDING</span
                    >
                </div>
                <div v-else-if="countdownActionType === 'upcoming'" class="flex items-center gap-2 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-secondary/80"></span>
                    <span
                        class="text-xs font-semibold text-secondary/80 tracking-widest"
                        >UPCOMING</span
                    >
                </div>
                <div v-else class="flex items-center gap-2 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-white/20"></span>
                    <span
                        class="text-xs font-semibold text-white/40 tracking-widest"
                        >ENDED</span
                    >
                </div>
            </div>

            <div class="grid lg:grid-cols-[1fr_420px] gap-8">
                <!-- Left: Image + details -->
                <div class="space-y-5">
                    <!-- Main image -->
                    <div
                        class="bg-surface-container rounded-3xl overflow-hidden aspect-[4/3] relative flex items-center justify-center"
                    >
                        <img
                            v-if="auction.image"
                            :src="`/storage/${auction.image}`"
                            :alt="auction.title"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="flex flex-col items-center text-white/10"
                        >
                            <svg
                                class="w-20 h-20"
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
                            <p class="mt-2 text-sm">No image provided</p>
                        </div>
                        <!-- Corner glow for active -->
                        <div
                            v-if="isLive && !countdown.ended"
                            class="absolute top-0 right-0 w-24 h-24 bg-primary/10 rounded-bl-full blur-2xl pointer-events-none"
                        ></div>
                    </div>

                    <!-- Info cards -->
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="bg-surface-container rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/40 mb-2">
                                Provenance
                            </p>
                            <p class="text-sm text-white/70 leading-relaxed">
                                {{
                                    auction.description ||
                                    "No additional details provided."
                                }}
                            </p>
                        </div>
                        <div class="bg-surface-container rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/40 mb-3">
                                Auction Details
                            </p>
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40"
                                        >Starting Price</span
                                    >
                                    <span class="font-display font-semibold"
                                        >${{
                                            Number(
                                                auction.starting_price,
                                            ).toLocaleString()
                                        }}</span
                                    >
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40"
                                        >Total Bids</span
                                    >
                                    <span class="font-display font-semibold">{{
                                        auction.bids?.length ?? 0
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-white/40">Seller</span>
                                    <span
                                        class="font-semibold text-secondary"
                                        >{{
                                            auction.user?.name ?? "Unknown"
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Bid panel -->
                <div class="space-y-4">
                    <!-- Current bid card -->
                    <div class="bg-surface-container rounded-3xl p-6">
                        <div class="flex items-start justify-between mb-1">
                            <p
                                class="text-xs text-white/40 font-semibold tracking-widest"
                            >
                                CURRENT HIGHEST BID
                            </p>
                            <span
                                v-if="isHighestBidder"
                                class="flex items-center gap-1.5 bg-primary/20 text-primary text-xs font-semibold px-2.5 py-1 rounded-full"
                            >
                                <svg
                                    class="w-3 h-3"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Leading
                            </span>
                        </div>
                        <p class="font-display text-4xl font-bold mt-1">
                            ${{ Number(currentPrice).toLocaleString() }}
                        </p>

                        <!-- Countdown -->
                        <div class="mt-5">
                            <p
                                class="text-xs text-white/40 font-semibold tracking-widest mb-3 uppercase"
                            >
                                {{ countdownActionType === 'upcoming' ? 'STARTS IN' : 'TIME REMAINING' }}
                            </p>
                            <div
                                v-if="!countdown.ended"
                                class="flex items-end gap-3"
                            >
                                <div class="text-center">
                                    <p class="font-display text-3xl font-bold">
                                        {{ pad(countdown.hours) }}
                                    </p>
                                    <p class="text-xs text-white/30 mt-1">
                                        HOURS
                                    </p>
                                </div>
                                <p
                                    class="font-display text-2xl font-bold text-white/30 mb-1"
                                >
                                    :
                                </p>
                                <div class="text-center">
                                    <p class="font-display text-3xl font-bold">
                                        {{ pad(countdown.mins) }}
                                    </p>
                                    <p class="text-xs text-white/30 mt-1">
                                        MINS
                                    </p>
                                </div>
                                <p
                                    class="font-display text-2xl font-bold text-white/30 mb-1"
                                >
                                    :
                                </p>
                                <div class="text-center">
                                    <p
                                        class="font-display text-3xl font-bold text-vivid"
                                    >
                                        {{ pad(countdown.secs) }}
                                    </p>
                                    <p class="text-xs text-white/30 mt-1">
                                        SECS
                                    </p>
                                </div>
                            </div>
                            <p
                                v-else
                                class="font-display text-xl font-bold text-white/40"
                            >
                                Auction Ended
                            </p>
                        </div>
                    </div>

                    <!-- Bid form -->
                    <div
                        v-if="!isSeller && isLoggedIn && countdownActionType === 'live'"
                        class="bg-surface-container rounded-3xl p-6 space-y-4"
                    >
                        <!-- Success -->
                        <div
                            v-if="successMsg"
                            class="flex items-center gap-2 bg-primary/10 border border-primary/30 rounded-2xl px-4 py-3"
                        >
                            <svg
                                class="w-4 h-4 text-primary shrink-0"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p class="text-sm text-primary">{{ successMsg }}</p>
                        </div>

                        <!-- Errors -->
                        <div
                            v-if="Object.keys(errors).length"
                            class="bg-vivid/10 border border-vivid/30 rounded-2xl px-4 py-3"
                        >
                            <p
                                v-for="(err, key) in errors"
                                :key="key"
                                class="text-sm text-vivid"
                            >
                                {{ err }}
                            </p>
                        </div>

                        <!-- Bid input -->
                        <div class="relative">
                            <span
                                class="absolute left-4 top-3.5 text-white/40 font-display font-bold"
                                >$</span
                            >
                            <input
                                v-model="bidAmount"
                                type="number"
                                :min="Number(currentPrice) + 1"
                                step="1"
                                :placeholder="
                                    (Number(currentPrice) + 100).toString()
                                "
                                class="pb-input pl-8 font-display text-lg"
                            />
                        </div>

                        <!-- Quick bid buttons -->
                        <div class="grid grid-cols-3 gap-2">
                            <button
                                v-for="q in quickBidAmounts"
                                :key="q.label"
                                type="button"
                                @click="placeInstantBid(q.value)"
                                :disabled="isSubmitting"
                                class="py-2.5 rounded-xl text-xs font-semibold bg-surface-bright hover:bg-white/10 text-white/70 hover:text-white transition-all shadow-ambient hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed border border-white/5"
                            >
                                Instant {{ q.label }}
                            </button>
                        </div>

                        <!-- Place bid CTA -->
                        <button
                            @click="placeBid"
                            :disabled="isSubmitting || !bidAmount"
                            class="w-full pb-btn-primary py-4 text-base flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="isSubmitting"
                                class="w-5 h-5 animate-spin"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            {{ isSubmitting ? "Placing Bid..." : "Place Bid" }}
                        </button>
                    </div>

                    <!-- Seller notice -->
                    <div
                        v-if="isSeller"
                        class="bg-surface-container rounded-3xl p-5 text-center"
                    >
                        <p class="text-sm text-white/40">
                            You are the seller of this auction.
                        </p>
                    </div>

                    <div
                        v-if="!isLoggedIn && countdownActionType === 'live'"
                        class="bg-surface-container rounded-3xl p-5 text-center space-y-3"
                    >
                        <p class="text-sm text-white/50">
                            Sign in to place a bid
                        </p>
                        <Link
                            :href="route('login')"
                            class="pb-btn-primary inline-block text-sm py-2.5 px-6"
                            >Log In to Bid</Link
                        >
                    </div>
                    
                    <div
                        v-if="countdownActionType === 'upcoming'"
                        class="bg-surface-container rounded-3xl p-5 text-center"
                    >
                        <p class="text-sm text-white/50">
                            Bidding opens soon. Stay tuned!
                        </p>
                    </div>

                    <!-- Bid history -->
                    <div class="bg-surface-container rounded-3xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="font-display font-semibold">
                                Bid History
                            </p>
                            <span
                                class="text-xs text-white/40 bg-surface-bright px-2.5 py-1 rounded-full"
                                >{{ auction.bids?.length ?? 0 }} TOTAL
                                BIDS</span
                            >
                        </div>

                        <div
                            v-if="!auction.bids?.length"
                            class="text-center py-6 text-white/30 text-sm"
                        >
                            No bids yet. Be the first!
                        </div>

                        <div v-else class="space-y-3">
                            <div
                                v-for="(bid, index) in [...(auction.bids || [])]
                                    .sort((a, b) => b.amount - a.amount)
                                    .slice(0, 6)"
                                :key="bid.id"
                                class="flex items-center gap-3 py-2"
                                :class="
                                    index < auction.bids.length - 1
                                        ? 'border-b border-white/5'
                                        : ''
                                "
                            >
                                <!-- Avatar -->
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                    :class="
                                        bid.user_id === authUserId
                                            ? 'bg-primary text-surface-lowest'
                                            : 'bg-surface-bright text-white/60'
                                    "
                                >
                                    {{
                                        bid.user_id === authUserId
                                            ? "YOU"
                                            : bid.user?.name
                                                  ?.charAt(0)
                                                  .toUpperCase()
                                    }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">
                                        {{
                                            bid.user_id === authUserId
                                                ? "You"
                                                : bid.user?.name
                                        }}
                                    </p>
                                    <p class="text-xs text-white/30">
                                        {{
                                            bid.created_at
                                                ? new Date(
                                                      bid.created_at,
                                                  ).toLocaleTimeString()
                                                : ""
                                        }}
                                    </p>
                                </div>
                                <p
                                    class="font-display font-bold text-sm"
                                    :class="
                                        index === 0
                                            ? 'text-primary'
                                            : 'text-white/70'
                                    "
                                >
                                    ${{ Number(bid.amount).toLocaleString() }}
                                </p>
                            </div>
                        </div>

                        <button
                            v-if="auction.bids?.length > 6"
                            class="w-full mt-4 py-2.5 text-xs font-semibold text-white/40 hover:text-white bg-surface-bright rounded-2xl transition-colors"
                        >
                            VIEW ALL ACTIVITY
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>
