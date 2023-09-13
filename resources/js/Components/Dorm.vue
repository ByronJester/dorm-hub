<script>
import { ref, onMounted} from 'vue';
import { router } from '@inertiajs/vue3'
import { MapboxMap, MapboxMarker } from '@studiometa/vue-mapbox-gl';

export default {
    props: ['dorm', 'user'],
    components: {
        MapboxMap,
        MapboxMarker,
    },
    setup(props){
        console.log(props.dorm)
        return {
            props
        }
    }
}
</script>

<template>
    <div class="w-full flex flex-col">
        <div class="w-full flex flex-col md:flex-row px-5">
            <div class="w-full md:mr-3">
                <p class="text-sm">
                    Dorm Image
                </p>

                <img :src="dorm.dorm_image"
                    class="generic-image"
                >
            </div>

            <div class="w-full md:mr-3">
                <p class="text-sm">
                    Map Location
                </p>

                <MapboxMap
                    class="generic-image"
                    access-token="pk.eyJ1IjoiYmFsb2dzeHh4IiwiYSI6ImNsbHA1dDN2MDAydGczZXFqZHprcW44dXIifQ.Z0dcyAB1W1B4-jcaqC_NKA"
                    map-style="mapbox://styles/mapbox/streets-v11"
                    :center="[dorm.long, dorm.lat]"
                    :zoom="15"
                >
                    <MapboxMarker :lng-lat="[dorm.long, dorm.lat]"/>
                </MapboxMap>
            </div>

            <div class="w-full">
                <p class="text-sm">
                    Business Permit
                </p>

                <img :src="dorm.business_permit_image"
                    class="generic-image"
                >
            </div>
        </div>

        <div class="w-full px-5 pt-2 md:pt-4">
            <div class="w-full">
                <p class="text-sm">
                    Rooms
                </p>
            </div>

            <div class="w-full grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="w-full flex flex-col"
                    v-for="(room, index) in props.dorm.rooms" :key="index"
                >
                    <div class="w-full cursor-pointer"
                    >
                        <img :src="room.image"
                            class="room-image"
                        >
                    </div>

                    <div class="w-full mt-3">
                        <p class="text-sm" font-bold>
                            Description:
                        </p>

                        <p class="text-xs">
                            {{ room.type_of_room }},
                            {{ room.furnished_type }},
                            {{ room.is_aircon == 'Yes' ? 'Air Condition' : 'Not Air Condition' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>


    </div>

</template>

<style>
.room-image {
    width: 100%;
    height: 220px;
    border-radius: 5px;
    border: 1px solid gray;

}

.generic-image {
    width: 100%;
    height: 300px;
    border-radius: 5px;
    border: 1px solid black;
}
</style>
