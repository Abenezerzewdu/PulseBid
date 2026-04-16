<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PulseBidLayout from '@/Layouts/PulseBidLayout.vue';
import { onMounted, ref } from 'vue';

const steps = [
    {
        number: '01',
        title: 'Discover',
        subtitle: 'Explore live auctions',
        description: 'Browse items people are auctioning right now — or list your own in seconds.',
        color: 'primary',
        icon: 'search'
    },
    {
        number: '02',
        title: 'Bid Live',
        subtitle: 'Place bids in real-time',
        description: 'Jump into auctions and compete instantly. Every bid updates live — no refresh needed.',
        color: 'secondary',
        icon: 'gavel'
    },
    {
        number: '03',
        title: 'Stay in the Game',
        subtitle: 'Timing is everything',
        description: 'Watch the countdown, react fast, and stay ahead of other bidders.',
        color: 'vivid',
        icon: 'clock'
    },
    {
        number: '04',
        title: 'Win',
        subtitle: 'Highest bid takes it',
        description: 'When the timer ends, the top bidder wins automatically. Simple and fair.',
        color: 'primary',
        icon: 'trophy'
    },
    {
        number: '05',
        title: 'Connect',
        subtitle: 'Chat with the seller',
        description: 'A private chat opens instantly so you can finalize the details.',
        color: 'secondary',
        icon: 'message'
    },
    {
        number: '06',
        title: 'Complete',
        subtitle: 'Finish the deal',
        description: 'Agree on delivery and payment directly — no middle steps.',
        color: 'vivid',
        icon: 'check'
    }
];

const visibleSteps = ref([]);

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const index = parseInt(entry.target.dataset.index);
                if (!visibleSteps.value.includes(index)) {
                    visibleSteps.value.push(index);
                }
            }
        });
    }, { threshold: 0.2 });

    document.querySelectorAll('.step-card').forEach(el => observer.observe(el));
});
</script>

<template>
    <Head title="How it Works — PulseBid" />
    <PulseBidLayout>
        <div class="relative overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-[120px] -z-10"></div>
            <div class="absolute top-1/2 right-1/4 w-96 h-96 bg-secondary/10 rounded-full blur-[120px] -z-10"></div>
            <div class="absolute bottom-0 left-1/3 w-96 h-96 bg-vivid/5 rounded-full blur-[120px] -z-10"></div>

            <!-- Hero Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold tracking-widest uppercase mb-6 animate-fade-in">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    Ecosystem Guide
                </div>
                <h1 class="font-display text-5xl sm:text-7xl font-bold tracking-tight mb-6 bg-gradient-to-b from-white to-white/60 bg-clip-text text-transparent">
                    How PulseBid Works
                </h1>
                <p class="max-w-2xl mx-auto text-white/50 text-lg leading-relaxed mb-10">
                    Your gateway to the global live auction marketplace. From discovery to delivery, we've streamlined every step of the journey.
                </p>
                <div class="h-1 w-20 bg-primary/40 mx-auto rounded-full"></div>
            </div>

            <!-- Steps Section -->
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 space-y-32 sm:space-y-48">
                <div 
                    v-for="(step, index) in steps" 
                    :key="index"
                    class="step-card group relative grid md:grid-cols-2 items-center gap-12 sm:gap-24 transition-all duration-700"
                    :data-index="index"
                    :class="[
                        visibleSteps.includes(index) ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12',
                        index % 2 === 0 ? '' : 'md:flex-row-reverse'
                    ]"
                >
                    <!-- Visual Side -->
                    <div 
                        class="relative aspect-square flex items-center justify-center p-8 rounded-4xl bg-surface-container/30 border border-white/5 backdrop-blur-sm overflow-hidden"
                        :class="index % 2 === 0 ? 'md:order-1' : 'md:order-2'"
                    >
                        <!-- Decorative Shapes -->
                        <div class="absolute inset-0 opacity-20 group-hover:opacity-40 transition-opacity duration-500">
                             <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,var(--tw-gradient-from)_0%,transparent_70%)]" :class="`from-${step.color}/20`"></div>
                        </div>

                        <!-- Animated Graphics -->
                        <div class="relative z-10 w-full h-full flex items-center justify-center">
                            <!-- Step 1: Discover -->
                            <div v-if="index === 0" class="relative">
                                <div class="w-32 h-32 rounded-3xl border-2 border-primary/40 flex items-center justify-center animate-float">
                                    <svg class="w-16 h-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <div class="absolute -top-4 -right-4 w-12 h-12 rounded-2xl bg-surface-bright flex items-center justify-center border border-white/10 animate-pulse-slow">
                                    <div class="w-6 h-1 bg-primary/40 rounded-full"></div>
                                </div>
                                <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-2xl bg-surface-bright/50 border border-white/10 p-3 space-y-2 animate-float" style="animation-delay: -1.5s">
                                    <div class="w-full h-1 bg-white/10 rounded-full"></div>
                                    <div class="w-2/3 h-1 bg-white/10 rounded-full"></div>
                                </div>
                            </div>

                            <!-- Step 2: Bid Live -->
                            <div v-if="index === 1" class="relative">
                                <div class="w-32 h-32 bg-secondary/5 rounded-full flex items-center justify-center border border-secondary/20 scale-110">
                                    <svg class="w-16 h-16 text-secondary animate-gavel" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10a4 4 0 014-4h10a4 4 0 014 4v11m-4-11V7a4 4 0 00-8 0v3" />
                                    </svg>
                                </div>
                                <div class="absolute inset-0 w-full h-full rounded-full border-2 border-secondary/20 animate-ping-slow"></div>
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-surface-bright px-3 py-1 rounded-full border border-secondary/40 text-[10px] font-bold text-secondary animate-bounce">
                                    + $500
                                </div>
                            </div>

                            <!-- Step 3: Stay in Game -->
                            <div v-if="index === 2" class="relative h-48 w-48 flex items-center justify-center">
                                <div class="absolute inset-0 rounded-full border-4 border-white/5 border-t-vivid animate-spin-slow"></div>
                                <div class="text-center font-display">
                                    <p class="text-3xl font-bold text-white mb-1 tracking-tighter">00:15</p>
                                    <p class="text-[10px] text-vivid font-bold tracking-widest uppercase">Remaining</p>
                                </div>
                                <div class="absolute top-2 w-2 h-2 rounded-full bg-vivid animate-ping"></div>
                            </div>

                            <!-- Step 4: Win -->
                            <div v-if="index === 3" class="relative">
                                <div class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center animate-celebrate">
                                    <svg class="w-16 h-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </div>
                                <div class="absolute -top-10 left-0 w-3 h-3 bg-secondary rounded-full animate-float" style="animation-delay: -0.5s"></div>
                                <div class="absolute top-4 -right-8 w-2 h-2 bg-vivid rounded-full animate-float" style="animation-delay: -1.2s"></div>
                                <div class="absolute bottom-0 -left-6 w-2 h-2 bg-primary rounded-full animate-float" style="animation-delay: -0.8s"></div>
                            </div>

                            <!-- Step 5: Connect -->
                            <div v-if="index === 4" class="relative w-48 h-32">
                                <div class="absolute left-0 top-4 w-24 h-16 bg-surface-bright rounded-2xl border border-white/10 flex items-center justify-center animate-float">
                                    <div class="space-y-1.5 w-2/3">
                                        <div class="h-1 w-full bg-secondary/30 rounded-full"></div>
                                        <div class="h-1 w-1/2 bg-white/10 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="absolute right-0 bottom-0 w-24 h-16 bg-secondary/10 rounded-2xl border border-secondary/20 flex items-center justify-center animate-float" style="animation-delay: -1.5s">
                                    <div class="space-y-1.5 w-2/3">
                                        <div class="h-1 w-full bg-secondary/50 rounded-full"></div>
                                        <div class="h-1 w-3/4 bg-white/20 rounded-full"></div>
                                    </div>
                                </div>
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-surface border border-white/10 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Step 6: Complete -->
                            <div v-if="index === 5" class="relative">
                                <div class="w-32 h-32 rounded-4xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center border border-white/10 animate-scale-up">
                                    <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 bg-surface-bright px-4 py-2 rounded-2xl border border-white/5 shadow-glow animate-float">
                                    <div class="w-6 h-4 bg-primary/20 rounded-sm"></div>
                                    <div class="w-12 h-1.5 bg-white/20 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text Side -->
                    <div :class="index % 2 === 0 ? 'md:order-2' : 'md:order-1'">
                        <div class="flex items-center gap-4 mb-6">
                            <span 
                                class="text-4xl font-display font-bold text-white/10 select-none"
                            >
                                {{ step.number }}
                            </span>
                            <div class="h-px flex-1 bg-gradient-to-r from-white/10 to-transparent"></div>
                        </div>
                        
                        <div class="space-y-4">
                            <h2 
                                class="font-display text-4xl font-bold tracking-tight group-hover:translate-x-2 transition-transform duration-300"
                                :class="`text-${step.color}`"
                            >
                                {{ step.title }}
                            </h2>
                            <h3 class="text-xl font-semibold text-white/90">
                                {{ step.subtitle }}
                            </h3>
                            <p class="text-white/50 leading-relaxed text-lg">
                                {{ step.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
                <div class="relative bg-surface-container rounded-[3rem] p-12 sm:p-24 overflow-hidden border border-white/5 text-center group">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-[100px] group-hover:bg-primary/10 transition-colors"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-secondary/5 rounded-full blur-[100px] group-hover:bg-secondary/10 transition-colors"></div>
                    
                    <div class="relative z-10 max-w-2xl mx-auto">
                        <h2 class="font-display text-4xl sm:text-5xl font-bold mb-8 leading-tight">
                            Ready to experience the 
                            <span class="text-primary">future of auctions?</span>
                        </h2>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link href="/auctions" class="pb-btn-primary w-full sm:w-auto px-10 py-4 text-base">
                                Explore Live Auctions
                            </Link>
                            <Link href="/register" class="pb-btn-secondary w-full sm:w-auto px-10 py-4 text-base">
                                Join the Community
                            </Link>
                        </div>
                        <p class="mt-8 text-white/30 text-sm">
                            Join over <span class="text-white/60 font-semibold">12,000+ collectors</span> worldwide.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>

<style scoped>
.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.animate-pulse-slow {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-ping-slow {
    animation: ping 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-gavel {
    animation: gavel 1.5s ease-in-out infinite;
    transform-origin: bottom right;
}

@keyframes gavel {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(-30deg); }
    40% { transform: rotate(10deg); }
}

.animate-spin-slow {
    animation: spin 10s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-celebrate {
    animation: celebrate 2s ease-in-out infinite;
}

@keyframes celebrate {
    0%, 100% { transform: scale(1) rotate(0deg); }
    50% { transform: scale(1.1) rotate(10deg); }
}

.animate-scale-up {
    animation: scaleUp 1s ease-out forwards;
}

@keyframes scaleUp {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Custom Utilities for colors that might not be in the config as text- classes */
.text-primary { color: #a5ffb8; }
.text-secondary { color: #53a0ff; }
.text-vivid { color: #FF0099; }
.bg-primary { background-color: #a5ffb8; }
.bg-secondary { background-color: #53a0ff; }
.bg-vivid { background-color: #FF0099; }
.from-primary\/20 { --tw-gradient-from: rgba(165, 255, 184, 0.2); --tw-gradient-to: rgba(165, 255, 184, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
.from-secondary\/20 { --tw-gradient-from: rgba(83, 160, 255, 0.2); --tw-gradient-to: rgba(83, 160, 255, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
.from-vivid\/20 { --tw-gradient-from: rgba(255, 0, 153, 0.2); --tw-gradient-to: rgba(255, 0, 153, 0); --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to); }
</style>
