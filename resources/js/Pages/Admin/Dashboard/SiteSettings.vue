<script setup>
import { ref } from "vue";

import { useForm } from "@inertiajs/vue3";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import FormSwitch from "@/Components/FormSwitch.vue";
import Loading from "@/Components/Loading.vue";
import LogoInput from "@/Components/LogoInput.vue";
import LogoInputLocal from "@/Components/LogoInputLocal.vue";
import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
const props = defineProps({
	setting: { type: Object, required: true },
});
const form = useForm({
	logo: props.setting.logo,
	name: props.setting.name,
	twitter: props.setting.twitter,
	youtube: props.setting.youtube,
	telegram_group: props.setting.telegram_group,
	telegram_channel: props.setting.telegram_channel,
	discord: props.setting.discord,
	documentation: props.setting.documentation,
	rpc: props.setting.rpc,
	ankr: props.setting.ankr,
	infura: props.setting.infura,
	blast: props.setting.blast,
	chat: props.setting.chat,
	featured: props.setting.featured,
	logo_uri: null,
	logo_path: null,
	logo_upload: false,
});
const save = () => form.put(window.route("admin.settings.update", props.setting.id));
const all = ref(false);
const showing = ref("site");
</script>

<template>
	<form class="" @submit.prevent="save()">
		<RadioSelect
			v-model="showing"
			:options="[
				{ label: 'Site', value: 'site' },
				{ label: 'Rpc', value: 'rpc' },
				{ label: 'Links', value: 'social' },
			]"
		/>
		<CollapseTransition>
			<div v-show="showing == 'site'" class="card mt-6 card-border">
				<div class="card-body">
					<FormInput
						label="Site Name"
						class="mb-4"
						v-model="form.name"
						type="text"
						:error="form.errors.name"
					/>
					<div class="border p-3 border-gray-650 bg-gray-750/50">
						<h3 class="text-lg mb-4 !text-primary font-extralight">
							Site Logo
						</h3>
						<div class="gap-x-3 grid gap-3">
							<FormInput
								v-model="form.logo_uri"
								:disabled="form.logo_upload"
								placeholder="https://"
								:error="form.errors.logo_uri"
								:help="$t('Supports png, jpeg or svg')"
							>
								<template #label>
									<div class="flex mb-3">
										<span class="mr-3">
											{{ $t("Logo") }}
										</span>
										<label class="inline-flex items-center space-x-2">
											<input
												v-model="form.logo_upload"
												class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
												type="checkbox"
											/>
											<span>
												{{ $t("Upload to server") }}
											</span>
										</label>
									</div>
								</template>
							</FormInput>
							<template v-if="form.logo_upload">
								<LogoInput
									v-if="$page.props.s3"
									v-model="form.logo_uri"
									v-model:file="form.logo_path"
									auto
								/>
								<LogoInputLocal
									v-else
									v-model="form.logo_uri"
									v-model:file="form.logo_path"
								/>
							</template>
							<img
								v-else
								class="w-12 h-12 my-auto rounded-full b-0"
								:src="form.logo_uri ?? form.logo ?? fakeLogo"
							/>
						</div>
						<p v-if="form.errors.logo" class="text-red-500 mt-2">
							{{ form.errors.logo }}
						</p>
						<p v-else class="text-xs mt-2">
							{{ $t("") }}
						</p>
					</div>
					<div class="grid mt-4 gap-4">
						<FormSwitch v-model="form.chat">
							{{ $t("Enable Chat") }}
						</FormSwitch>
						<FormSwitch v-model="form.featured">
							{{ $t("Enable Featured Spawnpads") }}
						</FormSwitch>
						<div class="pt-5">
							<div class="flex items-center gap-3 justify-end">
								<PrimaryButton
									type="submit"
									size="xs"
									:disabled="form.processing"
								>
									<Loading
										class="mr-2 -ml-1 inline-block w-5 h-5"
										v-if="form.processing"
									/>
									{{ $t("Update Site Config") }}
								</PrimaryButton>
							</div>
						</div>
					</div>
				</div>
			</div>
		</CollapseTransition>
		<CollapseTransition>
			<div v-show="showing == 'rpc'" class="card mt-6 card-border">
				<div class="card-body">
					<div class="flex items-center justify-between mb-4">
						<h4>Choose RPC provider</h4>
					</div>
					<div class="grid">
						<RadioSelect
							v-model="form.rpc"
							class="mb-5"
							:options="$page.props.rpcs"
						/>
						<CollapseTransition>
							<FormInput
								label="Ankr Api key"
								v-show="form.rpc === 'ankr'"
								v-model="form.ankr"
								type="text"
								:error="form.errors.ankr"
							/>
						</CollapseTransition>
						<CollapseTransition>
							<FormInput
								label="Infura Key"
								v-model="form.infura"
								v-show="form.rpc === 'infura'"
								type="text"
								:error="form.errors.infura"
							/>
						</CollapseTransition>
						<CollapseTransition>
							<FormInput
								label="Blast Api key"
								v-model="form.blast"
								v-show="form.rpc === 'blast'"
								type="text"
								:error="form.errors.blast"
							/>
						</CollapseTransition>
						<div class="pt-5">
							<div class="flex items-center gap-3 justify-end">
								<PrimaryButton
									type="submit"
									size="xs"
									:disabled="form.processing"
								>
									<Loading
										class="mr-2 -ml-1 inline-block w-5 h-5"
										v-if="form.processing"
									/>
									{{ $t("Update Rpc Setting") }}
								</PrimaryButton>
							</div>
						</div>
					</div>
				</div>
			</div>
		</CollapseTransition>
		<CollapseTransition>
			<div v-show="showing === 'social'" class="card mt-6 card-border">
				<div class="card-body">
					<div class="flex items-center justify-between mb-4">
						<h4>Project Links</h4>
					</div>

					<div class="grid gap-5">
						<FormInput
							label="Twitter URL"
							v-model="form.twitter"
							type="text"
							:error="form.errors.twitter"
						/>
						<FormInput
							label="Telegram group link"
							v-model="form.telegram_group"
							type="text"
							:error="form.errors.telegram_group"
						/>
						<FormSwitch v-model="all"> Show Hidden Websites </FormSwitch>
						<CollapseTransition>
							<div v-show="all" class="grid gap-5">
								<FormInput
									label="Discord Invite"
									v-model="form.discord"
									type="text"
									:error="form.errors.discord"
								/>
								<FormInput
									label="Telegram channel subscription link"
									v-model="form.telegram_channel"
									type="text"
									:error="form.errors.telegram_channel"
								/>
								<FormInput
									label="Youtube Channel URL"
									v-model="form.youtube"
									type="text"
									:error="form.errors.youtube"
								/>
								<FormInput
									label="Documentation URL"
									v-model="form.documentation"
									type="text"
									:error="form.errors.documentation"
								/>
							</div>
						</CollapseTransition>
					</div>
					<div class="pt-5">
						<div class="flex items-center gap-3 justify-end">
							<PrimaryButton
								type="submit"
								size="xs"
								:disabled="form.processing"
							>
								<Loading
									class="mr-2 -ml-1 inline-block !w-5 !h-5"
									v-if="form.processing"
								/>
								{{ $t("Update Website links") }}
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</CollapseTransition>
	</form>
</template>
