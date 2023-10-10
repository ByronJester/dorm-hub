<script>
import { ref, onMounted} from 'vue';
import { router } from '@inertiajs/vue3'

export default {
    props: ['dorms', 'user'],
    setup(props){
        const viewDorm = (id) => {
            router.get(route('view.dorm', id));
        }

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2)

            return '₱ ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');;
        }

        return {
            props,
            viewDorm,
            moneyFormat
        }
    }
}
</script>

<template>
    <div class="w-full grid grid-cols-1 md:grid-cols-4 gap-4 pt-2 md:pt-10 px-5">
        <div class="w-full flex flex-col"
            v-for="(dorm, index) in props.dorms" :key="index"
        >
            <div class="w-full cursor-pointer"
                @click="viewDorm(dorm.id)"
            >
                <img :src="dorm.dorm_image"
                    class="dorm-image p-3"
                >
            </div>

            <div class="w-full">
                <p class="font-bold ml-4 text-sm">
                    {{ dorm.property_name }}
                </p>

                <p class="font-bold ml-4 text-xs mt-1">
                    {{moneyFormat(dorm.payment.range_from) }} - {{ moneyFormat(dorm.payment.range_to) }} Monthly
                </p>

                <button class="bg-cyan-500 ml-4 mt-2 text-xs py-1 px-3 rounded-md"
                    v-if="!!props.user && props.user.user_type == 'owner'"
                    @click="$emit('editDorm', dorm)"
                >
                    Edit
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.dorm-image {
    width: 100%;
    height: 200px;
    border-radius: 20px;
}
</style>
