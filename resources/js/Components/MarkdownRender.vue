<!-- MarkdownSlotRenderer.vue -->

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue';

import 'highlight.js/styles/github.css'; // You can change to any style you prefer
import Markdown from 'vue3-markdown-it';

const props = defineProps({
    // Allow passing any vue3-markdown-it options as props
    html: { type: Boolean, default: false },
    linkify: { type: Boolean, default: true },
    breaks: { type: Boolean, default: false },
    typographer: { type: Boolean, default: false },
    langPrefix: { type: String, default: 'language-' },
    highlight: { type: Object, default: null },
    emoji: { type: Object, default: null },
    toc: { type: Object, default: null },
    anchor: { type: Object, default: null },
    tasklists: { type: Object, default: null },
    plugins: { type: Array, default: () => [] },
    // Additional props for this component
    trim: { type: Boolean, default: true },
    preserveIndentation: { type: Boolean, default: false }
});

// State
const slotCapture = ref(null);
const capturedContent = ref('');
const capturing = ref(true);

// Computed property to collect all markdown-it related props
const markdownOptions = computed(() => {
    return {
        html: props.html,
        linkify: props.linkify,
        breaks: props.breaks,
        typographer: props.typographer,
        langPrefix: props.langPrefix,
        highlight: props.highlight,
        emoji: props.emoji,
        toc: props.toc,
        anchor: props.anchor,
        tasklists: props.tasklists,
        plugins: props.plugins
    };
});

// Process the captured slot content
const processSlotContent = () => {
    if (!slotCapture.value) return '';

    let content = slotCapture.value.innerHTML;

    // Convert HTML to plain text (to get the markdown source)
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = content;
    content = tempDiv.textContent || tempDiv.innerText || '';

    if (props.trim) {
        content = content.trim();
    }

    // Handle indentation if needed
    if (!props.preserveIndentation) {
        // Remove common leading whitespace from all lines (like a code block)
        const lines = content.split('\n');
        if (lines.length > 1) {
            // Find minimum indentation level
            const minIndent = lines
                .filter(line => line.trim().length > 0)
                .reduce((min, line) => {
                    const indent = line.match(/^\s*/)[0].length;
                    return indent < min ? indent : min;
                }, Infinity);

            if (minIndent < Infinity) {
                content = lines
                    .map(line => line.slice(minIndent))
                    .join('\n');
            }
        }
    }

    return content;
};

// Capture the slot content on mount
onMounted(async () => {
    // Wait for the next DOM update to ensure slot content is rendered
    await nextTick();

    // Capture and process the slot content
    capturedContent.value = processSlotContent();

    // Switch to display mode
    capturing.value = false;
});
</script>

<template>
    <div class="markdown-slot-renderer">
        <!-- Hidden container to capture slot content -->
        <div
            ref="slotCapture"
            v-if="capturing"
            class="slot-capture"
            style="display: none;"
        >
            <slot></slot>
        </div>

        <!-- Render the captured markdown content -->
        <div
            v-if="!capturing"
            class="markdown-content"
        >
            <Markdown
                :source="capturedContent"
                v-bind="markdownOptions"
            />
        </div>
    </div>
</template>

<style>
.markdown-content {
    /* Add your desired styling for the rendered markdown */
}
</style>