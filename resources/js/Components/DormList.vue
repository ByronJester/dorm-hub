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
    <div  v-for="(dorm, index) in props.dorms" :key="index" class="w-full cursor-pointer bg-white rounded-lg ">
    <div @click="viewDorm(dorm.id)">
        <img class="rounded-t-lg w-full h-64 shadow-lg " :src="dorm.dorm_image" alt="dorm image" />
        <div class="bg-red-400 py-2 px-3 text-white text-center text-xs" v-if="dorm && dorm.status=='decline'">
            <i class="fa-solid fa-circle-exclamation"></i> This dorm has been decline. Reason:{{ dorm.reason }}
        </div>
        <div class="bg-orange-400 py-2 px-3 text-white text-center text-xs" v-if="dorm && dorm.status=='pending'">
            <i class="fa-solid fa-circle-exclamation"></i> This dorm is waiting for system admin approval.
        </div>
    </div>


    <div class="pb-5 mt-4">
        <a href="#">
            <p class="text-xl font-semibold tracking-tight text-gray-900">{{ dorm.property_name }}</p>
        </a>
        <a href="#">
            <p class="text-sm font-semibold tracking-tight text-gray-400">{{ dorm.detailed_address }}</p>
        </a>

        <a href="#">
            <p class="text-sm font-semibold tracking-tight text-gray-400">{{ moneyFormat(dorm.range_from) }} - {{ moneyFormat(dorm.range_to) }}</p>
        </a>


    </div>

    <footer>
        <div>
            <button class="bg-orange-400 text-xs px-4 py-2 rounded-md"
                    v-if="!!props.user && props.user.user_type == 'owner'"
                    @click="$emit('editDorm', dorm)"
                >
                    Edit
        </button>
        </div>

    </footer>
</div>

</template>

<style>
.dorm-image {
    width: 100%;
    height: 200px;
    border-radius: 20px;
}
</style>
