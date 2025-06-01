<!-- LaunchpadChat.vue -->
<script setup>
import { nextTick, onMounted, ref, watch } from "vue";

import { PaperAirplaneIcon } from "@heroicons/vue/24/outline";
import { useForm } from "@inertiajs/vue3";
import { MessageSquareHeart } from "lucide-vue-next";

import BaseButton from "@/Components/BaseButton.vue";
import FormInput from "@/Components/FormInput.vue";
import Web3Auth from "@/Pages/Auth/Web3Auth.vue";

const props = defineProps({
    launchpadId: {
        type: Number,
        required: true,
    },
    devId: {
        type: Number,
        required: true,
    },
    initialMessages: {
        type: Array,
        default: () => [],
    },
});

const messages = ref([...props.initialMessages]);
const imagePreview = ref(null);
const uploadPath = ref(null);

const form = useForm({
    message: "",
    image_path: null,
    image_upload: false,
    launchpad_id: props.launchpadId,
});

// Websocket connection
onMounted(() => {
    window.Echo.channel(`launchpad.${props.launchpadId}`).listen(
        "NewMessage",
        (e) => {
            console.log(e);
            messages.value.push(e.message);
            scrollToBottom();
        },
    );
});

const scrollToBottom = () => {
    nextTick(() => {
        const container = document.querySelector(".messages-container");
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
};

const submitMessage = () => {
    if (!form.message && !form.image_path) return;

    form.post(window.route("msgs.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
            uploadPath.value = null;
        },
    });
};

watch(
    messages,
    () => {
        scrollToBottom();
    },
    { deep: true },
);
</script>

<template>
    <div class="flex flex-col h-[900px] bg-gray-850 p-4 rounded-lg shadow">
        <!-- Messages Container -->
        <div
            v-if="messages.length == 0"
            class="w-full flex flex-col items-center justify-center h-full"
        >
            <div class="p-8 bg-gray-800 rounded">
                <MessageSquareHeart class="w-16 h-16 stroke-[0.8]" />
                <h3 class="text-xl font-extralight">No chat messages found</h3>
                <h3>Be the first to Leave a message</h3>
            </div>
        </div>
        <div class="overflow-y-auto pb-4 messages-container">
            <div
                v-for="message in messages"
                :key="message.uuid"
                class="mb-4"
                :class="{
                    'flex justify-end':
                        message.user_id === $page.props.auth.user?.id,
                }"
            >
                <div
                    :class="{
                        'bg-blue-100 dark:bg-blue-900':
                            message.user_id === $page.props.auth.user?.id,
                        'bg-gray-100 dark:bg-gray-750':
                            message.user_id !== $page.props.auth.user?.id,
                    }"
                    class="rounded-lg p-3"
                >
                    <div v-if="message.user.nfts.length > 0">

                        <div class="bg-gray-800 p-2 rounded-lg mb-2 flex items-center">
                            <div class="mr-auto">
                                <h3>{{ message.user.name ||
                                    message.user.address.substring(0, 6) }}</h3>
                                <BaseButton
                                    v-if="message.user_id === devId"
                                    class="self-center  pointer-events-none"
                                    size="xss"
                                    outlined
                                >
                                    PROJECT DEV
                                </BaseButton>
                            </div>
                            <div
                                :key="nft.id"
                                v-for="nft in message.user.nfts.slice(0, 3)"
                                class="relative"
                            >
                                <img
                                    :src="nft.image"
                                    class="w-12 h-12 rounded-full mr-1"
                                    :alt="nft.name"
                                />
                                <div
                                    class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500/40 border-2 border-white rounded-full -top-2 -end-2 dark:border-white">
                                    {{ nft.balance }}</div>
                            </div>
                            <img
                                :src="message.user.profile_photo_url"
                                :alt="message.user.name"
                                class="w-12 h-12 rounded-full mr-2"
                            />

                        </div>
                        <div>

                            <p class="text-sm">{{ message.message }}</p>
                            <img
                                v-if="message.image"
                                :src="message.image"
                                class="mt-2 rounded-lg max-w-full h-auto"
                                @click="$emit('image-click', message.image)"
                            />
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex items-start"
                    >
                        <img
                            :src="message.user.profile_photo_url"
                            :alt="message.user.name"
                            class="w-8 h-8 rounded-full mr-2"
                        />
                        <div>
                            <div class="text-sm font-semibold mb-1">
                                {{
                                    message.user.name ||
                                    message.user.address.substring(0, 6)
                                }}
                                <BaseButton
                                    v-if="message.user_id === devId"
                                    class="self-center ml-3 pointer-events-none"
                                    size="xss"
                                    outlined
                                >
                                    DEV
                                </BaseButton>
                            </div>
                            <p class="text-sm">{{ message.message }}</p>
                            <img
                                v-if="message.image"
                                :src="message.image"
                                class="mt-2 rounded-lg max-w-full h-auto"
                                @click="$emit('image-click', message.image)"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Input Area -->
        <div class="pt-4">
            <form
                @submit.prevent="submitMessage"
                class="space-y-4"
            >
                <!-- Image Preview -->
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <FormInput
                            v-model="form.message"
                            type="text"
                            size="md"
                            placeholder="Type your message..."
                        ></FormInput>
                    </div>

                    <BaseButton v-if="$page.props.auth.user">
                        <PaperAirplaneIcon class="w-6 h-6" />
                    </BaseButton>
                    <Web3Auth
                        size="md"
                        v-else
                    />
                </div>
            </form>
        </div>
    </div>
</template>
