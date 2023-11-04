<script>
import { usePage, router } from "@inertiajs/vue3";
import {ref} from 'vue';

export default{
    props:{
        content: String
    }
}
</script>

<template>
    <div v-html="content"></div>
</template>
