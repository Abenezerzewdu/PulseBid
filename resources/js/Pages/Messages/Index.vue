<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from "vue";
import { Head, usePage, Link } from "@inertiajs/vue3";
import PulseBidLayout from "@/Layouts/PulseBidLayout.vue";
import axios from "axios";

const props = defineProps({
    conversations: Array,
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const selectedConversation = ref(null);
const messages = ref([]);
const newMessage = ref("");
const isSending = ref(false);
const chatContainer = ref(null);
const fileInput = ref(null);
const searchQuery = ref("");
const pollingInterval = ref(null);

// Filter conversations based on search
const filteredConversations = computed(() => {
    if (!searchQuery.value) return props.conversations;
    const query = searchQuery.value.toLowerCase();
    return props.conversations.filter(c => 
        c.other_user.name.toLowerCase().includes(query) || 
        c.auction.title.toLowerCase().includes(query)
    );
});

// Select a conversation and fetch messages
const selectConversation = async (conversation) => {
    selectedConversation.value = conversation;
    await fetchMessages();
    scrollToBottom();
    
    // Start polling for new messages in this conversation
    startPolling();
};

const fetchMessages = async () => {
    if (!selectedConversation.value) return;
    
    try {
        const response = await axios.get(route('messages.show', selectedConversation.value.id));
        messages.value = response.data.messages;
    } catch (error) {
        console.error("Error fetching messages:", error);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() && !fileInput.value?.files[0]) return;
    if (isSending.value) return;

    isSending.value = true;
    const formData = new FormData();
    formData.append('type', fileInput.value?.files[0] ? 'file' : 'text');
    if (newMessage.value.trim()) formData.append('content', newMessage.value);
    if (fileInput.value?.files[0]) formData.append('attachment', fileInput.value.files[0]);

    try {
        await axios.post(route('messages.store', selectedConversation.value.id), formData);
        newMessage.value = "";
        if (fileInput.value) fileInput.value.value = "";
        await fetchMessages();
        scrollToBottom();
    } catch (error) {
        console.error("Error sending message:", error);
    } finally {
        isSending.value = false;
    }
};

const triggerFileUpload = () => {
    fileInput.value.click();
};

const scrollToBottom = () => {
    nextTick(() => {
        if (chatContainer.value) {
            chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
        }
    });
};

const startPolling = () => {
    stopPolling();
    pollingInterval.value = setInterval(fetchMessages, 5000); // Poll every 5 seconds
};

const stopPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
    if (props.conversations.length > 0) {
        // Optionally auto-select the first conversation
        // selectConversation(props.conversations[0]);
    }
});

onUnmounted(() => {
    stopPolling();
});

// Auto-scroll when new messages arrive
watch(messages, () => {
    // Only scroll if user is already near bottom (v2 improvement)
    scrollToBottom();
});
</script>

<template>
    <Head title="Messages — PulseBid" />
    <PulseBidLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="bg-surface-container rounded-3xl overflow-hidden shadow-ambient border border-white/5 flex h-[calc(100vh-180px)]">
                <!-- Sidebar -->
                <div class="w-full md:w-80 border-r border-white/5 flex flex-col bg-surface-lowest/30">
                    <div class="p-6">
                        <h1 class="font-display text-2xl font-bold mb-4">Messages</h1>
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search conversations..."
                                class="w-full bg-surface-bright/50 border-0 rounded-2xl py-3 pl-10 pr-4 text-sm text-white/70 placeholder-white/20 focus:ring-1 focus:ring-primary/40 transition-all"
                            />
                            <svg class="absolute left-3.5 top-3.5 w-4 h-4 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div v-if="filteredConversations.length === 0" class="p-8 text-center">
                            <p class="text-sm text-white/20 italic">No conversations found</p>
                        </div>
                        <button
                            v-for="conv in filteredConversations"
                            :key="conv.id"
                            @click="selectConversation(conv)"
                            class="w-full text-left p-4 flex items-center gap-4 transition-all hover:bg-white/5 border-l-4"
                            :class="selectedConversation?.id === conv.id ? 'bg-primary/5 border-primary shadow-[inset_0_0_20px_rgba(165,255,184,0.05)]' : 'border-transparent'"
                        >
                            <!-- Avatar -->
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-surface-bright to-surface-container border border-white/10 flex items-center justify-center font-bold text-white/40 overflow-hidden shrink-0">
                                <img v-if="conv.other_user.avatar" :src="conv.other_user.avatar" class="w-full h-full object-cover" />
                                <span v-else>{{ conv.other_user.name.charAt(0).toUpperCase() }}</span>
                                <div v-if="conv.unread_count > 0" class="absolute bottom-0 right-0 w-3 h-3 bg-primary rounded-full border-2 border-surface-container"></div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start mb-0.5">
                                    <h3 class="text-sm font-semibold truncate" :class="conv.unread_count > 0 ? 'text-white' : 'text-white/80'">
                                        {{ conv.other_user.name }}
                                    </h3>
                                    <span class="text-[10px] text-white/20 uppercase tracking-tighter">
                                        {{ conv.latest_message ? formatDate(conv.latest_message.created_at) : '' }}
                                    </span>
                                </div>
                                <p class="text-xs truncate" :class="conv.unread_count > 0 ? 'text-primary font-medium' : 'text-white/40'">
                                    {{ conv.latest_message?.content || 'Started a conversation' }}
                                </p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col bg-surface-lowest/10 relative">
                    <template v-if="selectedConversation">
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-white/5 flex items-center justify-between backdrop-blur-md bg-surface-container/30 sticky top-0 z-10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-surface-bright flex items-center justify-center font-bold text-white/30 overflow-hidden">
                                     <img v-if="selectedConversation.other_user.avatar" :src="selectedConversation.other_user.avatar" class="w-full h-full object-cover" />
                                     <span v-else>{{ selectedConversation.other_user.name.charAt(0).toUpperCase() }}</span>
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold leading-tight">{{ selectedConversation.other_user.name }}</h2>
                                    <p class="text-[10px] text-primary flex items-center gap-1 uppercase tracking-widest font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                        LIVE AUCTION ACTIVE
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2 text-white/30">
                                <button class="p-2 hover:bg-white/5 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg></button>
                                <button class="p-2 hover:bg-white/5 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg></button>
                                <button class="p-2 hover:bg-white/5 rounded-full transition-colors"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>
                            </div>
                        </div>

                        <!-- Messages Bodies -->
                        <div 
                            ref="chatContainer"
                            class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar scroll-smooth"
                        >
                            <div v-for="(msg, index) in messages" :key="index" class="flex flex-col" :class="msg.sender_id === currentUser.id ? 'items-end' : 'items-start'">
                                <div 
                                    class="max-w-[75%] rounded-2xl relative p-4 transition-all duration-300 transform"
                                    :class="msg.sender_id === currentUser.id 
                                        ? 'bg-primary text-surface-lowest rounded-tr-none font-medium shadow-[0_4px_15px_-3px_rgba(165,255,184,0.4)]' 
                                        : 'bg-surface-bright/50 text-white rounded-tl-none border border-white/5'"
                                >
                                    <p v-if="msg.type === 'text'" class="text-sm leading-relaxed whitespace-pre-wrap">{{ msg.content }}</p>
                                    
                                    <!-- File Attachment Rendering -->
                                    <div v-if="msg.type === 'file'" class="mt-2 bg-black/10 rounded-xl p-3 flex items-center gap-3 min-w-[240px]">
                                        <div class="w-10 h-10 rounded-lg bg-black/20 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold truncate">Attachment</p>
                                            <p class="text-[10px] opacity-40 font-mono">PDF DOCUMENT</p>
                                        </div>
                                        <a :href="msg.attachment_url" target="_blank" class="p-2 hover:bg-black/10 rounded-full">
                                            <svg class="w-4 h-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    </div>

                                    <div class="flex items-center gap-1 mt-2 float-right opacity-40">
                                        <span class="text-[9px] uppercase tracking-tighter">{{ formatDate(msg.created_at) }}</span>
                                        <svg v-if="msg.sender_id === currentUser.id" class="w-2.5 h-2.5" :class="msg.read_at ? 'text-surface-lowest' : ''" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                        </svg>
                                    </div>
                                    <div class="clear-both"></div>
                                </div>
                            </div>
                            
                            <!-- Status Indicator Bubble Example -->
                            <div class="flex justify-center py-4">
                                <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-4 py-1.5 rounded-full flex items-center gap-2 uppercase tracking-widest border border-secondary/20 shadow-glow-blue">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                                    Auction Starts In 18:42
                                </span>
                            </div>
                        </div>

                        <!-- Chat Input -->
                        <div class="p-6 bg-surface-lowest/50 backdrop-blur-md border-t border-white/5">
                            <form @submit.prevent="sendMessage" class="flex items-center gap-3">
                                <button type="button" @click="triggerFileUpload" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-surface-bright/50 text-white/40 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                </button>
                                <input ref="fileInput" type="file" @change="sendMessage" class="hidden" />

                                <div class="flex-1 relative">
                                    <input
                                        v-model="newMessage"
                                        type="text"
                                        placeholder="Type your message..."
                                        class="w-full bg-surface-bright/30 border-0 rounded-2xl py-3 pl-4 pr-10 text-sm focus:ring-1 focus:ring-primary/40 transition-all placeholder-white/10"
                                        :disabled="isSending"
                                    />
                                    <button type="button" class="absolute right-3 top-3 text-white/10 hover:text-white/40 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </button>
                                </div>

                                <button 
                                    type="submit" 
                                    class="w-10 h-10 flex items-center justify-center rounded-2xl bg-primary text-surface-lowest shadow-glow hover:scale-105 active:scale-95 transition-all"
                                    :disabled="isSending"
                                >
                                    <svg v-if="!isSending" class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                    <svg v-else class="w-5 h-5 animate-spin" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </button>
                            </form>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-12 opacity-20">
                        <div class="w-24 h-24 rounded-full bg-surface-bright flex items-center justify-center mb-6">
                            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                        </div>
                        <h2 class="font-display text-2xl font-bold mb-2">Select a Conversation</h2>
                        <p class="text-sm max-w-xs mx-auto">Click on a conversation in the sidebar to start messaging your seller or buyer.</p>
                    </div>
                </div>
            </div>
        </div>
    </PulseBidLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.1);
}
</style>
