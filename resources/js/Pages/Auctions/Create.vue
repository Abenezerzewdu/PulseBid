<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import PulseBidLayout from '@/Layouts/PulseBidLayout.vue';

const page = usePage();
const errors = computed(() => page.props.errors || {});

const form = ref({
    title: '',
    description: '',
    starting_price: '',
    reserve_price: '',
    start_time: '',
    duration: '24h',
    image: null,
    extra_images: [],
});

const imagePreview = ref(null);
const extraPreviews = ref([null, null, null]);
const isDragging = ref(false);
const isSubmitting = ref(false);

const durationOptions = [
    { label: '24H', value: '24h', hours: 24 },
    { label: '3 Days', value: '3d', hours: 72 },
    { label: '7 Days', value: '7d', hours: 168 },
];

const handleMainImage = (file) => {
    if (!file) return;
    form.value.image = file;
    const reader = new FileReader();
    reader.onload = (e) => { imagePreview.value = e.target.result; };
    reader.readAsDataURL(file);
};

const handleDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    if (file) handleMainImage(file);
};

const handleExtraImage = (e, index) => {
    const file = e.target.files[0];
    if (!file) return;
    form.value.extra_images[index] = file;
    const reader = new FileReader();
    reader.onload = (ev) => { extraPreviews.value[index] = ev.target.result; };
    reader.readAsDataURL(file);
};

const computedEndTime = computed(() => {
    if (!form.value.start_time) return '';
    const start = new Date(form.value.start_time);
    const hours = durationOptions.find(d => d.value === form.value.duration)?.hours || 24;
    start.setHours(start.getHours() + hours);
    return start.toISOString().slice(0, 16);
});

const relativeStartTime = computed(() => {
    if (!form.value.start_time) return '';
    const start = new Date(form.value.start_time).getTime();
    const now = new Date().getTime();
    const diffInMs = start - now;
    
    if (Math.abs(diffInMs) < 60000) return 'Starts now';
    
    const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });
    const diffInMinutes = Math.round(diffInMs / 60000);
    
    if (Math.abs(diffInMinutes) < 60) {
        return `Starts ${rtf.format(diffInMinutes, 'minute')}`;
    }
    
    const diffInHours = Math.round(diffInMinutes / 60);
    if (Math.abs(diffInHours) < 24) {
        return `Starts ${rtf.format(diffInHours, 'hour')}`;
    }
    
    const diffInDays = Math.round(diffInHours / 24);
    return `Starts ${rtf.format(diffInDays, 'day')}`;
});

onMounted(() => {
    // Set initial start time to exactly now (local time)
    const now = new Date();
    // adjust for local timezone to format as YYYY-MM-DDTHH:mm
    const tzOffsetMs = now.getTimezoneOffset() * 60000;
    const localISO = new Date(now.getTime() - tzOffsetMs).toISOString().slice(0, 16);
    form.value.start_time = localISO;
});

const submit = () => {
    isSubmitting.value = true;
    router.post('/auctions', {
        title: form.value.title,
        description: form.value.description,
        starting_price: form.value.starting_price,
        start_time: form.value.start_time,
        end_time: computedEndTime.value,
        image: form.value.image,
    }, {
        forceFormData: true,
        onFinish: () => { isSubmitting.value = false; },
    });
};
</script>

<template>
    <Head title="Launch Auction — PulseBid" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Back -->
            <Link href="/dashboard" class="inline-flex items-center gap-2 text-xs text-white/40 hover:text-primary transition-colors mb-8">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </Link>

            <!-- Header -->
            <div class="mb-10">
                <h1 class="font-display text-4xl sm:text-5xl font-bold">Launch a New Auction</h1>
                <p class="mt-3 text-white/50 max-w-xl">Present your masterpiece to the global marketplace. Fill in the details below to begin the live bidding process.</p>
            </div>

            <div class="grid lg:grid-cols-[380px_1fr] gap-8">
                <!-- Left: Image upload -->
                <div class="space-y-4">
                    <!-- Main image drop zone -->
                    <div
                        class="relative bg-surface-container rounded-3xl overflow-hidden aspect-square flex flex-col items-center justify-center cursor-pointer border-2 transition-all duration-200"
                        :class="isDragging ? 'border-primary/60 bg-primary/5' : 'border-transparent hover:border-white/10'"
                        @dragover.prevent="isDragging = true"
                        @dragleave="isDragging = false"
                        @drop.prevent="handleDrop"
                        @click="$refs.mainInput.click()"
                    >
                        <input ref="mainInput" type="file" accept="image/*" class="hidden" @change="e => handleMainImage(e.target.files[0])"/>

                        <template v-if="imagePreview">
                            <img :src="imagePreview" class="absolute inset-0 w-full h-full object-cover rounded-3xl"/>
                            <div class="absolute inset-0 bg-surface-lowest/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity rounded-3xl">
                                <p class="text-sm text-white font-medium">Change Image</p>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w-14 h-14 rounded-2xl bg-surface-bright flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="font-display font-semibold text-white">Upload Main Asset</p>
                            <p class="text-xs text-white/40 mt-1 text-center px-6">Drag and drop high-resolution images or click to browse</p>
                            <p class="text-xs text-white/20 mt-3">JPG, PNG, WEBP • MAX 10MB</p>
                        </template>
                    </div>
                    <p v-if="errors.image" class="text-xs text-vivid">{{ errors.image }}</p>

                    <!-- Extra images -->
                    <div class="grid grid-cols-3 gap-3">
                        <div
                            v-for="i in 3"
                            :key="i"
                            class="aspect-square bg-surface-container rounded-2xl flex items-center justify-center cursor-pointer hover:bg-surface-bright transition-colors relative overflow-hidden"
                            @click="$refs[`extra${i}`][0].click()"
                        >
                            <input :ref="`extra${i}`" type="file" accept="image/*" class="hidden" @change="e => handleExtraImage(e, i-1)"/>
                            <img v-if="extraPreviews[i-1]" :src="extraPreviews[i-1]" class="absolute inset-0 w-full h-full object-cover"/>
                            <svg v-else class="w-6 h-6 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Pro tip -->
                    <div class="bg-surface-container rounded-2xl p-4 border border-primary/20">
                        <p class="text-xs font-semibold text-primary mb-1">Pro Tip</p>
                        <p class="text-xs text-white/50 leading-relaxed">
                            Items with high-resolution, well-lit photographs receive up to
                            <span class="text-primary font-semibold">45% higher engagement</span>
                            and more competitive bidding wars.
                        </p>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="space-y-5">
                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Auction Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="e.g., Rare 1964 Chronograph Prototype"
                            class="pb-input"
                            :class="errors.title ? 'ring-2 ring-vivid/50' : ''"
                        />
                        <p v-if="errors.title" class="text-xs text-vivid mt-1">{{ errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Detailed Description</label>
                        <textarea
                            v-model="form.description"
                            rows="5"
                            placeholder="Describe the item's history, condition, and unique characteristics..."
                            class="pb-input resize-none"
                        ></textarea>
                    </div>

                    <!-- Prices -->
                    <div class="bg-surface-container rounded-3xl p-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Starting Price</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-white/40 font-display font-bold">$</span>
                                    <input
                                        v-model="form.starting_price"
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="pb-input pl-8 font-display"
                                        :class="errors.starting_price ? 'ring-2 ring-vivid/50' : ''"
                                    />
                                </div>
                                <p v-if="errors.starting_price" class="text-xs text-vivid mt-1">{{ errors.starting_price }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Reserve Price <span class="text-white/20">(Optional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-white/40 font-display font-bold">$</span>
                                    <input
                                        v-model="form.reserve_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="pb-input pl-8 font-display"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div class="bg-surface-container rounded-3xl p-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Start Date & Time</label>
                                <div class="relative">
                                    <input
                                        v-model="form.start_time"
                                        type="datetime-local"
                                        class="pb-input pr-10"
                                        :class="errors.start_time ? 'ring-2 ring-vivid/50' : ''"
                                    />
                                    <svg class="absolute right-3 top-3 w-4 h-4 text-white/30 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p v-if="relativeStartTime" class="text-xs text-primary mt-1">{{ relativeStartTime }}</p>
                                <p v-if="errors.start_time" class="text-xs text-vivid mt-1">{{ errors.start_time }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-white/50 mb-2 tracking-wide">Duration</label>
                                <div class="flex gap-2">
                                    <button
                                        v-for="opt in durationOptions"
                                        :key="opt.value"
                                        type="button"
                                        @click="form.duration = opt.value"
                                        class="flex-1 py-3 rounded-2xl text-sm font-display font-semibold transition-all duration-200"
                                        :class="form.duration === opt.value
                                            ? 'bg-primary text-surface-lowest shadow-glow'
                                            : 'bg-surface-bright text-white/50 hover:text-white'"
                                    >
                                        {{ opt.label }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-2">
                        <button
                            @click="submit"
                            :disabled="isSubmitting"
                            class="w-full pb-btn-primary py-4 text-base flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ isSubmitting ? 'Launching...' : 'Launch Auction' }}
                        </button>
                        <p class="text-center text-xs text-white/30 mt-3">
                            By clicking Launch, you agree to PulseBid's
                            <a href="#" class="text-primary hover:underline">Seller Agreement</a>
                            and commission structure.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>
