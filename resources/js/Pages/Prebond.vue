<script setup>
import Markdown from "vue3-markdown-it"; /* PartiallyEnd: #3632/scriptSetup.vue */

import AppLayout from "@/Layouts/AppLayout.vue";
const terms = `# Prebond Mechanism

## What is Prebonding?

Prebonding is a special early investment phase for the launchpads that creates a safer environment for all investors. It's designed to align the interests of early buyers (like the team and early supporters) with the long-term success of the project.

## How Prebonding Works

1. **Early Investment with Lock Period**: During the prebond phase, team and early supporters can purchase tokens at the lowest possible price, but with a temporary lock on selling.

2. **Special Protection**: Your prebonded tokens remain locked until the entire bonding process completes. This means early buyers (including the team) cannot sell their tokens and abandon the project early.

3. **Alignment of Interests**: This mechanism ensures that early investors and team members remain committed to supporting and promoting the token until the bonding target is reached.

## The Prebonding Contribution Guideline

1. **Choose Your Investment**: Select how much BNB you want to contribute to the prebonding phase. You will buying at the lowest possible price.

2. **Review and Confirm**: You'll see how many tokens you'll receive. The tokens will not be sent to your wallet address, but remain locked until the bonding phase is complete.

3. **Complete Purchase**: Click the "BUY" button to complete your prebond investment.

4. **Wait for Token Release**: Your tokens will become available for withdrawal once the project reaches its funding target and the bonding phase is complete.

## Why This Benefits You

- **Protection Against Rug Pulls**: The development team and early investors cannot sell their tokens and abandon the project early.

- **Better Price Stability**: Prevents early dumps that could crash the token price.

- **Team Commitment**: Ensures the team remains motivated to promote and develop the project until bonding is complete.

- **Early Investor Advantage**: You get the lowest possible token price as an early supporter.

## Important Notes

- The prebond phase offers the lowest possible token price.
- Tokens purchased during prebond cannot be sold until the bonding target is reached.
- Once bonding is complete, you can withdraw your tokens to any address you choose.
- The token will be available for trading on decentralized exchanges after the bonding phase completes.
`;
</script>

<template>
	<AppLayout>
		<div class="min-h-screen bg-gray900">
			<div class="max-w-6xl mx-auto px-4 py-8">
				<div
					class="prose markdown-content dark:prose-invert text-white max-w-none"
				>
					<Markdown class="gap-3" :source="terms" />
				</div>
			</div>
		</div>
	</AppLayout>
</template>
