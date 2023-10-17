<template>
    <div @click="toggle" v-on-clickaway class="relative">
      
      <slot/>
    </div>
  </template>
  
  <script>
  import { mixin as clickaway } from "vue3-click-away";
  
  export default {
    name: "AppDropdown",
    mixins: [clickaway],
    provide() {
      return {
        sharedState: this.sharedState
      };
    },
    data() {
      return {
        sharedState: {
          active: false
        }
      };
    },
    methods: {
      toggle() {
        this.sharedState.active = !this.sharedState.active;
      },
      away() {
        this.sharedState.active = false;
      }
    }
  };
  </script>
  