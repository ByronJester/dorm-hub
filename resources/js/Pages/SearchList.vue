<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { usePage } from "@inertiajs/vue3";

export default{
    components:{
        GuestLayout,
        AuthenticatedLayout
    },
    setup(){
        const page = usePage();
        const user = page.props.auth.user;
        return{
            user
        }
    }
}
</script>

<template>
<GuestLayout v-if="user===null">
    <div class="max-w-[2520px] mt-20 xl:px-20 md:px-10 sm:px-2 px-4">
        <div
            className="
                        max-w-screen-lg
                        mx-auto
                        "
        >
        <form>   
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" v-bind="query" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 " placeholder="Search Location..." required>
                <a :href="route('search.result')" class="text-white absolute right-2.5 bottom-2.5 bg-orange-400 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-4 py-2">Search</a>
            </div>
        </form>
        </div>
    </div>
</GuestLayout>
<AuthenticatedLayout v-else>
    <div class="max-w-[2520px] mt-16 xl:px-20 md:px-10 sm:px-2 px-4">
        <div
            className="
                        max-w-screen-lg
                        mx-auto
                        "
        >
            yow
        </div>
    </div>
</AuthenticatedLayout>
</template>