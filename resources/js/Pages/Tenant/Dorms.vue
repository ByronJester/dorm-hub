<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { ref, reactive, watch, onMounted, computed } from "vue";
import { MapboxMap, MapboxMarker } from "@studiometa/vue-mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import axios from "axios";
import { router } from "@inertiajs/vue3";
import DormList from "@/Components/DormList.vue";
import Hero from "@/Components/Hero.vue";

export default {
    components: {
        AuthenticatedLayout,
        DormList,
        Hero
    },
    setup() {
        const page = usePage();
        const user = computed(() => page.props.auth.user);
        const dorms = ref([]);
        const hero = page.props.hero;
        console.log(page.props);
        onMounted(() => {
            dorms.value = page.props.dorms;
        });

        return {
            user,
            dorms,
            hero,
        };
    },
};
</script>

<template>
    <AuthenticatedLayout>
        <Hero :title="hero.title" :subtitle="hero.subtitle" :description="hero.description" :heroImage="hero.image_path"/>
        <div class="max-w-[2520px] mx-auto xl:px-20 md:px-10 sm:px-2 px-4">

            
            <div
                className="
            grid 
            grid-cols-1 
            sm:grid-cols-2 
            md:grid-cols-3 
            lg:grid-cols-4
            xl:grid-cols-4
            2xl:grid-cols-6
            gap-8
          "
            >
                <!--
            -->
                <DormList :dorms.sync="dorms" :user.sync="user"/>
                
            </div>
        </div>
    </AuthenticatedLayout>
</template>
