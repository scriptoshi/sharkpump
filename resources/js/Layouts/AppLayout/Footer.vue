<script setup>
import { Link } from "@inertiajs/vue3";
import { BookOpenCheckIcon, LucideSend } from "lucide-vue-next";
import { CoBolt } from "oh-vue-icons/icons";
import { DiscordIcon, XIcon, YouTubeIcon } from "vue3-simple-icons";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
</script>
<template>
    <footer class="bg-gray-800 text-gray-300">
        <div class="bg-gradient-to-b from-primary/15 via-black to-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-8">
                    <!-- Brand Column -->
                    <div class="space-y-4 col-span-2">
                        <Link
                            class="flex items-center"
                            href="/"
                        >
                        <ApplicationLogo class="block h-8 w-auto font-semibold" />
                        </Link>

                        <p class="text-sm font-semibold">
                            {{
                                $t(
                            "Swim with the Sharks, Launch with the Best. Make Waves, Not Ripples"
                            )
                            }}
                        </p>
                    </div>
                    <!-- Explore Column -->
                    <div>
                        <ul class="space-y-4">
                            <li>
                                <Link
                                    href="/prebond-mechanism"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                {{ $t("Prebond Mechanism") }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/fees"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                {{ $t("Fees") }}
                                </Link>
                            </li>
                            <li class="hidden">
                                <aside
                                    href="#"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    {{ $t("Rewards") }}
                                </aside>
                            </li>
                            <li class="hidden">
                                <a
                                    href="#"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    {{ $t("Contributions") }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Resources Column -->
                    <div>
                        <ul class="space-y-4">
                            <li>
                                <Link
                                    href="/privacy"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                {{ $t("Policy") }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/terms"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                {{ $t("Terms") }}
                                </Link>
                            </li>
                            <li class="hidden">
                                <Link
                                    href="#"
                                    class="text-sm font-semibold hover:text-primary transition-colors"
                                >
                                {{ $t("How it works") }}
                                </Link>
                            </li>
                            <li
                                class="hidden"
                                v-if="$page.props.links.documentation"
                            >
                                <a
                                    :href="$page.props.links.documentation"
                                    class="inline-flex items-center space-x-2 text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    <BookOpenCheckIcon class="w-5 h-5" />
                                    <span>{{ $t("Docs") }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Social & Subscribe Column -->
                    <div>
                        <ul class="space-y-4">
                            <li v-if="$page.props.links.twitter">
                                <a
                                    :href="$page.props.links.twitter"
                                    class="inline-flex items-center space-x-2 text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    <XIcon class="w-4 h-4" />
                                    <span>{{ $t("Profile") }}</span>
                                </a>
                            </li>
                            <li v-if="$page.props.links.tgGroup">
                                <a
                                    :href="$page.props.links.tgGroup"
                                    class="inline-flex items-center space-x-2 text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    <LucideSend class="w-5 h-5" />

                                    <span>{{ $t("Telegram") }}</span>
                                </a>
                            </li>

                            <li
                                class="hidden"
                                v-if="$page.props.links.discord"
                            >
                                <a
                                    :href="$page.props.links.discord"
                                    class="inline-flex items-center space-x-2 text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    <DiscordIcon class="w-5 h-5" />
                                    <span>Discord</span>
                                </a>
                            </li>
                            <li
                                class="hidden"
                                v-if="$page.props.links.youtube"
                            >
                                <a
                                    :href="$page.props.links.youtube"
                                    class="inline-flex items-center space-x-2 text-sm font-semibold hover:text-primary transition-colors"
                                >
                                    <YouTubeIcon class="w-5 h-5" />
                                    <span>Discord</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-white text-sm font-medium">
                            {{ $t("Follow Progress:") }}
                        </h3>
                        <!-- Social Links -->
                        <!-- Telegram Bot Button -->
                        <Link href="/status">
                            <PrimaryButton class="w-full justify-center">
                                <VueIcon :icon="CoBolt" />
                                {{ $t("Status Page") }}
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
