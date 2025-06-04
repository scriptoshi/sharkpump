<script setup>
import { Users, UserCheck, Crown } from "lucide-vue-next";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    stats: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-900">
            <div class="max-w-6xl mx-auto px-4 py-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        Platform Status
                    </h1>
                    <p class="text-xl text-gray-400">
                        Real-time statistics about our growing community
                    </p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <!-- Total Users -->
                    <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-primary/50 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-blue-600/20 rounded-lg">
                                <Users class="w-8 h-8 text-blue-400" />
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-white">
                                    {{ stats.totalUsers.toLocaleString() }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    Total Users
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm">
                            Total number of registered users on the platform
                        </p>
                    </div>

                    <!-- Verified Users -->
                    <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-primary/50 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-green-600/20 rounded-lg">
                                <UserCheck class="w-8 h-8 text-green-400" />
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-white">
                                    {{ stats.verifiedUsers.toLocaleString() }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    Verified Users
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm">
                            Users who have completed email verification
                        </p>
                    </div>

                    <!-- Premium Buyers -->
                    <div class="bg-gray-800 rounded-lg p-8 border border-gray-700 hover:border-primary/50 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-yellow-600/20 rounded-lg">
                                <Crown class="w-8 h-8 text-yellow-400" />
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-white">
                                    {{ stats.premiumBuyers.toLocaleString() }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    Premium Buyers
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm">
                            Users who have minted at least one NFT
                        </p>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Registration Target Progress -->
                    <div class="bg-gray-800 rounded-lg p-8 border border-gray-700">
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-white mb-4">
                                Registration Target
                            </h3>
                            <div class="mb-6">
                                <div class="text-4xl font-bold text-primary mb-2">
                                    {{ Math.round((stats.totalUsers / 10000) * 100) }}%
                                </div>
                                <div class="text-gray-400">
                                    {{ stats.totalUsers.toLocaleString() }} / 10,000 users
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-700 rounded-full h-4 mb-4">
                                <div 
                                    class="bg-gradient-to-r from-primary to-blue-500 h-4 rounded-full transition-all duration-1000 relative overflow-hidden"
                                    :style="{ width: Math.min((stats.totalUsers / 10000) * 100, 100) + '%' }"
                                >
                                    <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                </div>
                            </div>
                            
                            <p class="text-gray-300 text-sm">
                                {{ (10000 - stats.totalUsers).toLocaleString() }} more users to reach our target
                            </p>
                        </div>
                    </div>

                    <!-- Community Health -->
                    <div class="bg-gray-800 rounded-lg p-8 border border-gray-700">
                        <div class="text-center">
                            <h3 class="text-xl font-bold text-white mb-4">
                                Community Health
                            </h3>
                            <p class="text-gray-300 text-lg mb-6">
                                Our community continues to grow with dedicated users and premium members driving the platform forward.
                            </p>
                            
                            <!-- Verification Rate -->
                            <div class="max-w-md mx-auto">
                                <div class="flex justify-between text-sm text-gray-400 mb-2">
                                    <span>Verification Rate</span>
                                    <span>{{ Math.round((stats.verifiedUsers / stats.totalUsers) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-700 rounded-full h-2">
                                    <div 
                                        class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all duration-1000"
                                        :style="{ width: Math.round((stats.verifiedUsers / stats.totalUsers) * 100) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Growing Strong Section -->
                <div class="bg-gray-800 rounded-lg p-8 border border-gray-700">
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white mb-4">
                            Growing Strong
                        </h2>
                        
                        <!-- Milestone Progress -->
                        <div class="max-w-2xl mx-auto">
                            <div class="flex justify-between text-sm text-gray-400 mb-4">
                                <span>0</span>
                                <span>2.5K</span>
                                <span>5K</span>
                                <span>7.5K</span>
                                <span class="text-primary font-semibold">10K Target</span>
                            </div>
                            
                            <!-- Milestone markers -->
                            <div class="relative">
                                <div class="w-full bg-gray-700 rounded-full h-3">
                                    <div 
                                        class="bg-gradient-to-r from-primary via-blue-500 to-purple-500 h-3 rounded-full transition-all duration-1000 relative"
                                        :style="{ width: Math.min((stats.totalUsers / 10000) * 100, 100) + '%' }"
                                    >
                                        <div class="absolute right-0 top-0 w-4 h-4 bg-white rounded-full transform translate-x-2 -translate-y-0.5 shadow-lg"></div>
                                    </div>
                                </div>
                                
                                <!-- Milestone dots -->
                                <div class="absolute top-0 w-full h-3 flex justify-between items-center">
                                    <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                    <div class="w-2 h-2 bg-gray-600 rounded-full" :class="{ 'bg-primary': stats.totalUsers >= 2500 }"></div>
                                    <div class="w-2 h-2 bg-gray-600 rounded-full" :class="{ 'bg-primary': stats.totalUsers >= 5000 }"></div>
                                    <div class="w-2 h-2 bg-gray-600 rounded-full" :class="{ 'bg-primary': stats.totalUsers >= 7500 }"></div>
                                    <div class="w-3 h-3 bg-primary rounded-full border-2 border-white" :class="{ 'animate-pulse': stats.totalUsers >= 10000 }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer note -->
                <div class="text-center mt-8">
                    <p class="text-gray-500 text-sm">
                        Statistics are updated in real-time and reflect the current state of the platform.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
