<script setup>
import ChainSymbol from "@/Components/ChainSymbol.vue";
import Loading from "@/Components/Loading.vue";
import { useBillions } from "@/hooks";
import { useFactoryConfig } from "@/hooks/useContractCall";
const props = defineProps({
    rate: Number,
    factory: Object
});
const config = useFactoryConfig(
    props.factory.factory_abi,
    props.factory.contract,
);

</script>
<template>
    <li class="flex items-center font-lg text-yellow-400">
        <Loading
            v-if="config.loading"
            class="w-5 h-5 mr-2"
        />
        {{ config.bondingTarget }}
        <ChainSymbol :chain-id="factory.chainId" /> (~${{
            useBillions(config.bondingTarget * rate)
        }})
    </li>
</template>
