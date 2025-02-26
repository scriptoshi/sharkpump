<script setup>
import { ref, watch } from 'vue';

import LiteYouTubeEmbed from 'vue-lite-youtube-embed';

import CollapseTransition from '@/Components/CollapseTransition.vue';
import FormSwitch from '@/Components/FormSwitch.vue';
import YoutubeInput from '@/Components/YoutubeInput.vue';
import 'vue-lite-youtube-embed/style.css';
const props = defineProps({
    launchpad: Object
});
const changeLiveStream = ref(!props.launchpad.livestreamId);
watch(() => props.launchpad.livestreamId, (livestreamId) => {
    changeLiveStream.value = !livestreamId;
});
</script>
<template>
    <div>
        <CollapseTransition>
            <FormSwitch
                v-show="launchpad.livestreamId"
                v-model="changeLiveStream"
            >Change Livestream Url
            </FormSwitch>
        </CollapseTransition>
        <CollapseTransition>
            <YoutubeInput
                v-show="changeLiveStream"
                :launchpad="launchpad"
                v-if="launchpad.isOwner"
            />
        </CollapseTransition>
        <LiteYouTubeEmbed
            v-if="!!launchpad.livestreamId"
            :id="launchpad.livestreamId"
            :title="`${launchpad.name} Developer Livestream`"
        />
        <div
            v-else
            class="w-full bg-gray-900 rounded-lg overflow-hidden"
        >
            <!-- Stream Container -->
            <div class="relative w-full h-[450px] bg-black">
                <!-- Placeholder Content -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <svg
                        class="w-20 h-20 mb-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                        />
                    </svg>
                    <h2 class="text-2xl font-bold mb-2">Coming Soon</h2>
                    <p class="text-gray-500">
                        Live streaming will be available shortly
                    </p>
                </div>
                <!-- Stream Controls -->
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/80 to-transparent">
                    <div class="flex items-center justify-between">
                        <!-- Left Controls -->
                        <div class="flex items-center space-x-4">
                            <button class="text-white hover:text-gray-300">
                                <svg
                                    class="w-8 h-8"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-6 h-6 text-white"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 001.48-5.34c-.47-2.78-2.79-5-5.59-5.34a6.505 6.505 0 00-7.27 7.27c.34 2.8 2.56 5.12 5.34 5.59a6.5 6.5 0 005.34-1.48l.27.28v.79l4.25 4.25c.41.41 1.08.41 1.49 0 .41-.41.41-1.08 0-1.49L15.5 14zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"
                                    />
                                </svg>
                                <span class="text-white">00:00 / --:--</span>
                            </div>
                        </div>
                        <!-- Right Controls -->
                        <div class="flex items-center space-x-4">
                            <button class="text-white hover:text-gray-300">
                                <svg
                                    class="w-6 h-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"
                                    />
                                </svg>
                            </button>
                            <button class="text-white hover:text-gray-300">
                                <svg
                                    class="w-6 h-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"
                                    />
                                </svg>
                            </button>
                            <button class="text-white hover:text-gray-300">
                                <svg
                                    class="w-6 h-6"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="w-full bg-gray-600 rounded-full h-1">
                            <div class="bg-blue-500 h-1 rounded-full w-0"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Stream Info -->
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">
                            Dev Live Stream
                        </h3>
                        <p class="text-sm text-gray-400">0 viewers</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Subscribe
                        </button>
                        <button class="p-2 text-gray-400 hover:text-white">
                            <svg
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                                />
                            </svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-white">
                            <svg
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
