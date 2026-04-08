<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    auction: Object,
    authUserId: Number,
});

const bidAmount = ref("");
const countdown = ref("");

// 🧠 Countdown logic
const updateCountdown = () => {
    const end = new Date(props.auction.end_time);
    const now = new Date();

    const diff = Math.floor((end - now) / 1000);

    if (diff <= 0) {
        countdown.value = "Ended";
        return;
    }

    const minutes = Math.floor(diff / 60);
    const seconds = diff % 60;

    countdown.value = `${minutes}m ${seconds}s`;
};

onMounted(() => {
    updateCountdown();
    setInterval(updateCountdown, 1000);
});

// 🧠 Place bid
const placeBid = () => {
    router.post(
        `/auctions/${props.auction.id}/bid`,
        {
            amount: bidAmount.value,
        },
        {
            onSuccess: () => {
                bidAmount.value = "";
            },
            onError: (errors) => {
                alert(Object.values(errors).join("\n"));
            },
        },
    );
};

const isSeller = props.authUserId === props.auction.user_id;
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <!-- Title -->
        <h1 class="text-2xl font-bold mb-4">
            {{ auction.title }}
        </h1>

        <!-- Price -->
        <p class="text-lg mb-2">
            Current Price:
            <strong>
                {{ auction.current_price ?? auction.starting_price }}
            </strong>
        </p>

        <!-- Countdown -->
        <p class="text-red-500 mb-4">Ends in: {{ countdown }}</p>

        <!-- Bid Input -->
        <div v-if="!isSeller" class="flex gap-2 mb-6">
            <input
                v-model="bidAmount"
                type="number"
                placeholder="Enter your bid"
                class="border px-3 py-2 rounded w-full"
            />
            <button
                @click="placeBid"
                class="bg-indigo-600 text-white px-4 py-2 rounded"
            >
                Bid
            </button>
        </div>

        <!-- Bid List -->
        <div>
            <h2 class="font-semibold mb-2">Bids</h2>

            <div v-if="auction.bids.length === 0">No bids yet</div>

            <div
                v-for="bid in auction.bids"
                :key="bid.id"
                class="border-b py-2"
            >
                {{ bid.user.name }} → {{ bid.amount }}
            </div>
        </div>
    </div>
</template>
