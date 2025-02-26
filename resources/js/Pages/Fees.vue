<script setup>
import Markdown from 'vue3-markdown-it'; /* PartiallyEnd: #3632/scriptSetup.vue */

import AppLayout from '@/Layouts/AppLayout.vue';
const terms = `# PumpFans.io Fee Structure

## Overview

PumpFans.io implements a transparent fee structure designed to maintain platform sustainability while offering competitive rates for users. This document outlines all fees associated with using our platform.

## Deployment Fees

When launching a new token or project on PumpFans.io, the following deployment fees apply:

- **Base Deployment Fee**: $50-100 USD
- **Fee Variation**: Depends on the blockchain network selected
  
The deployment fee covers the cost of smart contract deployment, initial setup, and integration with the PumpFans.io ecosystem.

## Trading Fees

### Bonding Curve Trades

When trading on our proprietary bonding curve mechanism:

- **Buy Trades**: No fee
- **Sell Trades**: 1% fee on the total transaction value

This fee structure encourages holding and strategic trading while providing necessary resources for platform maintenance and development.

## Liquidity Migration

When liquidity is migrated to external decentralized exchanges (specifically Uniswap):

- **Uniswap Swap Fee**: 0.3% per swap
  - These fees are charged by Uniswap as per their standard protocol
  - **Important**: These Uniswap fees can be claimed by the project developer

## Liquidity Token (NFT) Lockup

- **Lockup Period**: 10 years
- After the 10-year lockup period, the Liquidity Token (NFT) will be unlocked
- This long-term lockup ensures project stability and demonstrates our commitment to sustainable tokenomics

## Fee Allocation

All platform fees collected by PumpFans.io are allocated as follows:

- Platform development and maintenance
- Security audits and improvements
- User experience enhancements
- Community growth initiatives

## Fee Adjustments

PumpFans.io reserves the right to adjust the fee structure in response to market conditions, network fee fluctuations, or protocol upgrades. Any changes to the fee structure will be communicated to users with appropriate notice.

---

*Last Updated: February 26, 2025*

For additional questions regarding our fee structure, please contact our support team through the official channels listed on our website.`;
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-gray900">
            <div class="max-w-6xl mx-auto px-4 py-8">
                <div class="prose markdown-content dark:prose-invert text-white max-w-none">
                    <Markdown
                        class="gap-3"
                        :source="terms"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>