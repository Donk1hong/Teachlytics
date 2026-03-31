<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    chats: {
        type: Array,
        default: () => []
    },
    activeChatId: Number
});

const page = usePage();
const authId = computed(() => page.props.auth.user.id);
const selectedChatId = ref(null);
const typingUsers = ref({});
const onlineUsers = ref([]);
const messagesContainer = ref(null);

const localChats = ref([...props.chats]);

watch(() => props.chats, (newChats) => {
    localChats.value = [...newChats];
}, { deep: true });

const activeChat = computed(() =>
    localChats.value.find(c => c.id === selectedChatId.value)
);

const isOnline = (userId) => {
    return onlineUsers.value.some(user => user.id === userId);
};

const formatTime = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

watch(selectedChatId, () => {
    scrollToBottom();
});

const listenToChat = (id) => {
    if (typeof window.Echo === 'undefined') return;

    window.Echo.join(`chat.${id}`)
        .here((users) => {
            onlineUsers.value = [...onlineUsers.value, ...users];
        })
        .joining((user) => {
            if (!onlineUsers.value.find(u => u.id === user.id)) {
                onlineUsers.value.push(user);
            }
        })
        .leaving((user) => {
            onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
        })
        .listen('MessageSent', (e) => {
            const chat = localChats.value.find(c => c.id === id);
            if (chat) {
                const messageExists = chat.messages.some(m => m.id === e.message.id);
                if (!messageExists) {
                    chat.messages.push(e.message);
                    chat.last_message = e.message.text;
                    if (selectedChatId.value === id) {
                        scrollToBottom();
                    }
                }
            }
        })
        .listenForWhisper('typing', (e) => {
            typingUsers.value[id] = e.typing;
            setTimeout(() => {
                typingUsers.value[id] = false;
            }, 3000);
        });
};

const onTyping = () => {
    if (typeof window.Echo === 'undefined') return;

    window.Echo.join(`chat.${selectedChatId.value}`)
        .whisper('typing', {
            typing: true
        });
};

const form = useForm({
    conversation_id: null,
    text: ''
});

const sendMessage = () => {
    form.conversation_id = selectedChatId.value;
    form.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('text');
            scrollToBottom();
        }
    });
};

onMounted(() => {
    if (props.activeChatId && localChats.value.find(c => c.id === props.activeChatId)) {
        selectedChatId.value = props.activeChatId;
        localChats.value.forEach(chat => listenToChat(chat.id));
        scrollToBottom();
    } else if (localChats.value.length > 0) {
        selectedChatId.value = localChats.value[0].id;
        localChats.value.forEach(chat => listenToChat(chat.id));
        scrollToBottom();
    }
});

onUnmounted(() => {
    if (typeof window.Echo === 'undefined') return;
    localChats.value.forEach(chat => {
        window.Echo.leave(`chat.${chat.id}`);
    });
});
</script>

<template>
    <Head title="Сообщения" />

    <AuthenticatedLayout>
        <template #header>Сообщения</template>

        <div class="max-w-[1500px] mx-auto h-[calc(100vh-12rem)] flex gap-6 animate-slide-up pb-10">

            <div class="w-full lg:w-96 bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] flex flex-col overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800/50">
                    <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Чаты</h3>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
                    <div v-if="localChats.length === 0" class="text-center p-6 text-slate-500 font-bold text-sm">
                        У вас пока нет активных диалогов.
                    </div>

                    <button
                        v-for="chat in localChats"
                        :key="chat.id"
                        @click="selectedChatId = chat.id"
                        :class="['w-full p-4 rounded-3xl flex items-center gap-4 transition-all relative',
                        selectedChatId === chat.id ? 'bg-indigo-600 text-white shadow-lg' : 'hover:bg-slate-100 dark:hover:bg-slate-800/50']"
                    >
                        <div class="relative">
                            <img :src="chat.interlocutor.avatar" class="w-12 h-12 rounded-2xl object-cover shadow-sm" />
                            <div v-if="isOnline(chat.interlocutor.id)" class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-4 border-white dark:border-[#111827] rounded-full"></div>
                        </div>
                        <div class="text-left overflow-hidden">
                            <h4 class="font-bold truncate" :class="selectedChatId === chat.id ? 'text-white' : 'text-slate-900 dark:text-white'">{{ chat.interlocutor.name }}</h4>

                        </div>
                    </button>
                </div>
            </div>

            <div v-if="activeChat" class="flex-1 bg-white/60 dark:bg-[#111827]/60 backdrop-blur-2xl border border-white/50 dark:border-slate-800/80 rounded-[2.5rem] flex flex-col overflow-hidden shadow-2xl relative">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800/50 flex items-center gap-4 bg-white/40 dark:bg-[#111827]/40 backdrop-blur-md z-10">
                    <div class="relative">
                        <img :src="activeChat.interlocutor.avatar" class="w-12 h-12 rounded-2xl object-cover shadow-sm" />
                        <div v-if="isOnline(activeChat.interlocutor.id)" class="absolute -bottom-1 -right-1 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-[#111827] rounded-full"></div>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 dark:text-white tracking-tight">{{ activeChat.interlocutor.name }}</h3>
                        <p v-if="typingUsers[activeChat.id]" class="text-[10px] font-bold text-emerald-500 animate-pulse uppercase tracking-widest mt-0.5">
                            Печатает...
                        </p>
                        <p v-else class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">
                            {{ isOnline(activeChat.interlocutor.id) ? 'В сети' : 'Не в сети' }}
                        </p>
                    </div>
                </div>

                <div ref="messagesContainer" class="flex-1 overflow-y-auto p-8 space-y-8 custom-scrollbar flex flex-col bg-slate-50/30 dark:bg-[#0B0F19]/30">
                    <div
                        v-for="msg in activeChat.messages"
                        :key="msg.id"
                        :class="['max-w-[70%] p-5 rounded-3xl text-sm font-medium shadow-sm relative group',
                        msg.sender_id === authId
                        ? 'bg-indigo-600 text-white self-end rounded-tr-none'
                        : 'bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 text-slate-900 dark:text-white self-start rounded-tl-none']"
                    >
                        {{ msg.text }}
                        <span :class="['absolute -bottom-6 text-[10px] font-bold text-slate-400', msg.sender_id === authId ? 'right-0' : 'left-0']">
                            {{ formatTime(msg.created_at) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 bg-white/40 dark:bg-[#111827]/60 border-t border-slate-100 dark:border-slate-800/50 backdrop-blur-xl z-10">
                    <form @submit.prevent="sendMessage" class="flex gap-4">
                        <input
                            v-model="form.text"
                            @input="onTyping"
                            type="text"
                            placeholder="Введите сообщение..."
                            class="flex-1 h-14 px-6 rounded-2xl bg-slate-100 dark:bg-slate-900/80 border-none focus:ring-1 focus:ring-indigo-500/50 text-sm font-bold placeholder-slate-400 dark:placeholder-slate-500"
                        />
                        <button
                            type="submit"
                            :disabled="form.processing || !form.text.trim()"
                            class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg hover:bg-indigo-500 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i class="pi pi-send text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div v-else class="flex-1 flex flex-col items-center justify-center bg-white/40 dark:bg-[#111827]/40 rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-700 backdrop-blur-md">
                <i class="pi pi-comments text-6xl text-slate-300 dark:text-slate-600 mb-6"></i>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">Выберите чат для начала общения</p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #6366f133; border-radius: 10px; }
</style>
