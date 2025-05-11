<script setup>
import Markdown from "vue3-markdown-it"; /* PartiallyEnd: #3632/scriptSetup.vue */

import AppLayout from "@/Layouts/AppLayout.vue";
const terms = `# Fee Structure

## Overview

At Sharkpump.io, our transparent fee structure is designed for platform sustainability and offers competitive rates. This document details all fees associated with using our platform.

## Deployment Fees

Launching a new token or project on Sharkpump.io incurs a **Base Deployment Fee of 0.006 BNB**. The final cost may vary based on the chosen blockchain network. This fee covers smart contract deployment, initial setup, and integration within the Sharkpump.io ecosystem.

## Trading Fees

### Bonding Curve Trades

For trades executed via our proprietary bonding curve mechanism, a uniform **0.75% fee** is applied to both buy and sell transactions on the total value. This structure aims to incentivize holding and strategic trading, while also funding ongoing platform maintenance and development.

## Liquidity Migration

When liquidity is migrated to external decentralized exchanges, such as Uniswap, standard Uniswap swap fees of **0.3% per swap** apply. These fees are charged by Uniswap as per their protocol. It's important to note that **project developers can claim these Uniswap-charged fees.**

## Liquidity Providers (LP) Tokens Lockup

To ensure project stability and demonstrate our commitment to sustainable tokenomics, Liquidity Provider (LP) Tokens are subject to a **10-year lockup period**. Following this period, the LP Tokens will be unlocked.

## Fee Allocation

Fees collected by Sharkpump.io are strategically allocated to:

* Fund platform development and ongoing maintenance
* Conduct security audits and implement improvements
* Enhance the overall user experience
* Support community growth initiatives

## Fee Adjustments

Sharkpump.io reserves the right to modify its fee structure in response to evolving market conditions, network fee fluctuations, or protocol upgrades. Users will be notified of any such changes with appropriate advance notice.

---

*Last Updated: March 25, 2025*

For further inquiries about our fee structure, please contact our support team via the official channels on our website.`;
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
