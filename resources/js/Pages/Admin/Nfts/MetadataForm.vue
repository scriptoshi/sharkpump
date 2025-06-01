<script setup>
import { onMounted } from 'vue';

import { useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

import FormInput from '@/Components/FormInput.vue';
import FormTextarea from '@/Components/FormTextArea.vue';
import Loading from '@/Components/Loading.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    nft: {
        type: Object,
        required: true
    }
});

const form = useForm({
    name: '',
    description: '',
    image: '', // External URL for image
    external_url: '',
    animation_url: '', // Optional URL for 3D models, videos, etc
    background_color: '',
    attributes: [
        { trait_type: 'Membership Type', value: 'Standard' },
        { trait_type: 'Membership Level', value: '1' },
        { trait_type: 'Benefits', value: 'Basic Access' }
    ]
});

// Load existing metadata if available
onMounted(() => {
    if (props.nft.metadata) {
        form.name = props.nft.metadata.name || 'Members Soul Bound Token';
        form.description = props.nft.metadata.description || 'Members Soul Bound Token';
        form.image = props.nft.metadata.image || 'https://cdn.scriptoshi.com/logos/NiPrQ3PeNASmYVeYS9ua.png';
        form.external_url = props.nft.metadata.external_url || 'https://memex.scriptoshi.com';
        form.animation_url = props.nft.metadata.animation_url || '';
        form.background_color = props.nft.metadata.background_color || 'FF0000';
        if (props.nft.metadata.attributes) {
            form.attributes = props.nft.metadata.attributes;
        }
    }
});

const addAttribute = () => {
    form.attributes.push({
        trait_type: '',
        value: ''
    });
};

const removeAttribute = (index) => {
    form.attributes.splice(index, 1);
};

const save = () => {
    // Remove any empty attributes before saving
    form.attributes = form.attributes.filter(attr =>
        attr.trait_type.trim() !== '' && attr.value.trim() !== ''
    );

    form.put(window.route('admin.nfts.metadata.update', props.nft.id), {
        preserveScroll: true
    });
};
</script>

<template>
    <div class="card sm:p-12 h-full border-0 card-border">
        <div class="card-body card-gutterless h-full">
            <div class="grid gap-6">
                <div class="border-b border-gray-750 pb-6">
                    <h2 class="text-xl font-semibold mb-2">NFT Metadata</h2>
                    <p class="text-sm text-gray-500">
                        Set the standard metadata for your membership NFT collection.
                        This metadata will be used for all tokens in the collection.
                    </p>
                </div>

                <!-- Basic Metadata -->
                <div class="grid sm:grid-cols-2 gap-4">
                    <FormInput
                        label="Token Name"
                        v-model="form.name"
                        type="text"
                        help="Name displayed for the NFT"
                        :error="form.errors.name"
                        required
                    />

                    <FormInput
                        label="Background Color"
                        v-model="form.background_color"
                        type="text"
                        help="Hex color (e.g., FF0000 for red)"
                        :error="form.errors.background_color"
                        placeholder="Optional - 6 character hex"
                    />
                </div>

                <FormTextarea
                    label="Description"
                    v-model="form.description"
                    help="Detailed description of the membership NFT"
                    :error="form.errors.description"
                    required
                />

                <div class="grid sm:grid-cols-2 gap-4">
                    <FormInput
                        label="Image URL"
                        v-model="form.image"
                        type="url"
                        help="Direct link to NFT image (IPFS, Arweave, etc.)"
                        :error="form.errors.image"
                        required
                    />

                    <FormInput
                        label="External URL"
                        v-model="form.external_url"
                        type="url"
                        help="Link to your project website"
                        :error="form.errors.external_url"
                    />
                </div>

                <FormInput
                    label="Animation URL"
                    v-model="form.animation_url"
                    type="url"
                    help="Optional - URL for multimedia content (3D model, video, etc.)"
                    :error="form.errors.animation_url"
                />

                <!-- Attributes Section -->
                <div class="border-t border-gray-750 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Membership Attributes</h3>
                        <PrimaryButton
                            type="button"
                            @click="addAttribute"
                            secondary
                        >
                            Add Attribute
                        </PrimaryButton>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(attr, index) in form.attributes"
                            :key="index"
                            class="grid sm:grid-cols-2 gap-4 p-4 border border-gray-750 rounded-lg relative"
                        >
                            <FormInput
                                label="Trait Type"
                                v-model="attr.trait_type"
                                type="text"
                                :error="form.errors[`attributes.${index}.trait_type`]"
                            />
                            <FormInput
                                label="Value"
                                v-model="attr.value"
                                type="text"
                                :error="form.errors[`attributes.${index}.value`]"
                            />
                            <button
                                @click="removeAttribute(index)"
                                type="button"
                                class="absolute top-2 right-2 text-red-500 hover:text-red-600"
                                v-if="form.attributes.length > 1"
                            >
                                <span class="sr-only">Remove</span>
                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-750">
                    <div
                        class="py-2 px-8 mr-4 text-emerald-400 bg-emerald-800/20"
                        v-if="form.recentlySuccessful"
                    >
                        Saved succefully
                    </div>
                    <PrimaryButton
                        type="button"
                        secondary
                        :href="route('admin.nfts.index')"
                        link
                    >
                        <ArrowLeft class="mr-1 -ml-1 w-4 h-4" />
                        Cancel
                    </PrimaryButton>
                    <PrimaryButton
                        type="button"
                        @click="save"
                        :disabled="form.processing"
                    >
                        <Loading
                            class="mr-2 -ml-1 inline-block w-5 h-5"
                            v-if="form.processing"
                        />
                        Save Metadata
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </div>
</template>