<template>
    <carousel :items-to-show="1.5">
      <slide v-for="slide in 10" :key="slide">
        {{ slide }}
      </slide>

      <template #addons>
        <navigation />
        <pagination />
      </template>
    </carousel>
  </template>

<script>
     // If you are using PurgeCSS, make sure to whitelist the carousel CSS classes
    import 'vue3-carousel/dist/carousel.css'
    import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

    export default {
        name: 'App',
        components: {
            Carousel,
            Slide,
            Pagination,
            Navigation,
        },
    }
</script>
