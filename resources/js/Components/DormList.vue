<script>
import { ref, onMounted} from 'vue';

export default {
    props: ['dorms', 'user'],
    setup(props){
        return {
            props
        }
    }
}
</script>

<template>
    <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-4 pt-2 md:pt-10 px-5">
        <div class="w-full flex flex-col"
            v-for="(dorm, index) in props.dorms" :key="index"
        >
            <div class="w-full cursor-pointer">
                <img :src="dorm.dorm_image"
                    class="dorm-image p-3"
                >
            </div>

            <div class="w-full">
                <p class="font-bold ml-4 text-sm">
                    {{ dorm.detailed_address }}
                </p>

                <p class="font-bold ml-4 text-xs mt-1">
                    ₱ {{ dorm.payment.fee }}/month
                </p>
            </div>
        </div>
    </div>
</template>

<style>
.dorm-image {
    width: 100%;
    height: 300px;
    border-radius: 20px;
}
</style>
