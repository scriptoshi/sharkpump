<script setup>
import { ref } from "vue";

import { Head, Link } from "@inertiajs/vue3";
import {
	BaggageClaim,
	ChevronDown,
	LayoutPanelLeft,
	MessageSquareCode,
	Pickaxe,
	Tv2,
	Users,
	Zap,
	Warehouse,
} from "lucide-vue-next";

import ApplicationMark from "@/Components/ApplicationLogo.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import Web3Auth from "@/Pages/Auth/Web3Auth.vue";

defineProps({
	title: String,
});

const showingNavigationDropdown = ref(false);

const isCurrent = (routes) => routes.find((r) => window.route().current(r));

const menus = [
	{
		name: "Dashboard",
		icon: LayoutPanelLeft,
		route: "admin.dashboard",
		active: "admin.dashboard",
	},
	{
		name: "Factories",
		icon: Pickaxe,
		route: "admin.factories.index",
		active: "admin.factories.*",
	},
	{
		name: "Launchpads",
		icon: Zap,
		route: "admin.launchpads.index",
		active: "admin.launchpads.*",
	},
	{
		name: "Users",
		icon: Users,
		route: "admin.users.index",
		active: ["admin.users.*", "admin.trades.*", "admin.msgs.*"],
		submenu: [
			{
				name: "Users",
				icon: Users,
				route: "admin.users.index",
				active: "admin.users.*",
			},
			{
				name: "Trades",
				icon: BaggageClaim,
				route: "admin.trades.index",
				active: "admin.trades.*",
			},
			{
				name: "Messages",
				icon: MessageSquareCode,
				route: "admin.msgs.index",
				active: "admin.msgs.*",
			},
		],
	},
];

const links = [
	{
		name: "Factories",
		icon: Pickaxe,
		route: "admin.factories.index",
		active: "admin.products.*",
	},
	{
		name: "Launchpads",
		icon: Zap,
		route: "admin.launchpads.index",
		active: "admin.launchpads.*",
	},
	{
		name: "Users",
		icon: Users,
		route: "admin.users.index",
		active: "admin.users.*",
	},
	{
		name: "Trades",
		icon: BaggageClaim,
		route: "admin.trades.index",
		active: "admin.trades.*",
	},
	{
		name: "Messages",
		icon: MessageSquareCode,
		route: "admin.msgs.index",
		active: "admin.msgs.*",
	},
	{
		name: "Nfts",
		icon: Warehouse,
		route: "admin.nfts.index",
		active: "admin.nfts.*",
	},
	{
		name: "Promos",
		icon: Tv2,
		route: "admin.promos.index",
		active: "admin.promos.*",
	},
];
</script>

<template>
	<div>
		<Head :title="title" />

		<Banner />

		<div class="min-h-screen bg-white dark:bg-gray-850">
			<nav class="">
				<!-- Primary Navigation Menu -->
				<div class="container-fluid mx-auto px-4 sm:px-6 lg:px-8">
					<div class="flex justify-between h-16">
						<div class="flex">
							<!-- Logo -->
							<div class="shrink-0 flex items-center">
								<Link
									class="flex items-center"
									:href="route('admin.dashboard')"
								>
									<ApplicationMark
										class="block h-5 w-auto font-semibold"
									/>
								</Link>
							</div>

							<!-- Navigation Links -->
							<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
								<template v-for="menu in menus" :key="menu.name">
									<div
										v-if="menu.submenu"
										class="ms-3 flex items-center relative"
									>
										<Dropdown align="left" width="48">
											<template #trigger>
												<span class="inline-flex rounded-md">
													<button
														type="button"
														:class="
															isCurrent(menu.active)
																? 'text-primary'
																: 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
														"
														class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-white dark:bg-gray-800 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
													>
														<component
															:is="menu.icon"
															class="h-5 w-5 mr-2 -ml-1"
															:class="{
																'text-primary': isCurrent(
																	menu.active
																),
															}"
														/>

														{{ menu.name }}
														<ChevronDown
															class="ms-2 -me-0.5 h-4 w-4"
														/>
													</button>
												</span>
											</template>

											<template #content>
												<!-- Account Management -->
												<div
													class="block px-4 py-2 text-xs text-gray-400"
												>
													{{ menu.name }}
												</div>

												<DropdownLink
													v-for="sub in menu.submenu"
													:key="sub.name"
													:href="route(sub.route)"
													class="flex items-center"
													:active="route().current(sub.active)"
													as="a"
												>
													<component
														:is="sub.icon"
														class="h-5 w-5 mr-2 -ml-1"
													/>
													{{ sub.name }}
												</DropdownLink>
											</template>
										</Dropdown>
									</div>
									<NavLink
										v-else
										:href="route(menu.route)"
										:active="route().current(menu.active)"
									>
										<component
											:is="menu.icon"
											class="h-5 w-5 mr-2 -ml-1"
											:class="{
												'text-primary': route().current(
													menu.active
												),
											}"
											aria-hidden="true"
										/>
										{{ menu.name }}
									</NavLink>
								</template>
							</div>
						</div>

						<div class="flex items-center ms-6 gap-3">
							<!-- Settings Dropdown -->

							<div class="sm:ms-3 hidden sm:flex relative">
								<Dropdown align="right" width="48">
									<template #trigger>
										<span class="inline-flex rounded-md">
											<button
												type="button"
												class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
											>
												<img
													class="w-5 h-5 mr-2 rounded-full"
													:src="
														$page.props.auth.user
															.profile_photo_url
													"
												/>
												{{ $page.props.auth.user.name }}

												<svg
													class="ms-2 -me-0.5 h-4 w-4"
													xmlns="http://www.w3.org/2000/svg"
													fill="none"
													viewBox="0 0 24 24"
													stroke-width="1.5"
													stroke="currentColor"
												>
													<path
														stroke-linecap="round"
														stroke-linejoin="round"
														d="M19.5 8.25l-7.5 7.5-7.5-7.5"
													/>
												</svg>
											</button>
										</span>
									</template>

									<template #content>
										<!-- Account Management -->
										<div
											class="block px-4 py-2 text-xs text-gray-400"
										>
											Quick Links
										</div>

										<DropdownLink
											v-for="link in links"
											:key="link.name"
											:href="route(link.route)"
											as="a"
										>
											<component
												:is="link.icon"
												class="h-5 w-5 mr-2 -ml-1 inline-flex"
												:class="{
													'text-primary': route().current(
														link.active
													),
												}"
												aria-hidden="true"
											/>
											{{ link.name }}
										</DropdownLink>
									</template>
								</Dropdown>
							</div>
							<Web3Auth />
							<!-- Hamburger -->
							<div class="-me-2 flex items-center sm:hidden">
								<button
									class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
									@click="
										showingNavigationDropdown = !showingNavigationDropdown
									"
								>
									<svg
										class="h-6 w-6"
										stroke="currentColor"
										fill="none"
										viewBox="0 0 24 24"
									>
										<path
											:class="{
												hidden: showingNavigationDropdown,
												'inline-flex': !showingNavigationDropdown,
											}"
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M4 6h16M4 12h16M4 18h16"
										/>
										<path
											:class="{
												hidden: !showingNavigationDropdown,
												'inline-flex': showingNavigationDropdown,
											}"
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M6 18L18 6M6 6l12 12"
										/>
									</svg>
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Responsive Navigation Menu -->
				<div
					:class="{
						block: showingNavigationDropdown,
						hidden: !showingNavigationDropdown,
					}"
					class="sm:hidden"
				>
					<div class="pt-2 pb-3 space-y-1">
						<ResponsiveNavLink
							v-for="link in links"
							:key="link.name"
							:href="route(link.route)"
							:active="route().current(link.active)"
							class="flex flex-row items-start space-x-4 text-gray-300"
							as="a"
						>
							<component
								:is="link.icon"
								class="h-5 w-5 mr-2 -ml-1"
								:class="{
									'text-primary': route().current(link.active),
								}"
								aria-hidden="true"
							/>
							<span>{{ link.name }}</span>
						</ResponsiveNavLink>
					</div>

					<!-- Responsive Settings Options -->
				</div>
			</nav>

			<!-- Page Content -->
			<main class="container">
				<slot />
			</main>
		</div>
	</div>
</template>
