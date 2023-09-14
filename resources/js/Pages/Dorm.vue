<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { usePage } from '@inertiajs/vue3'
    import { router } from '@inertiajs/vue3'
    import Dorm from '@/Components/Dorm.vue';

    export default {
        components: {
            AuthenticatedLayout,
            Dorm
        },
        setup() {
            const page = usePage()

            const user = page.props.auth.user;
            const dorm = page.props.dorm;

            const back = () => {
                var url = null;

                if(user) {
                    url = user.user_type + '.dorms'
                } else {
                    url = 'landing.page'
                }

                router.get(route(url));
            }

            return {
                user,
                dorm,
                back
            }
        }
    }
</script>


<template>
    <AuthenticatedLayout v-if="user">
        <div class="main w-full flex flex-col" style="min-height: 92vh;">
            <!-- <div class="w-full mt-5 pl-5">
                <p class="cursor-pointer" @click="back()">
                    <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
                </p>
            </div> -->

            <div class="w-full mt-5">
                <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
            </div>
        </div>
    </AuthenticatedLayout>

    <div class="main w-full flex flex-col" v-else style="min-height: 100vh;">
        <!-- <div class="w-full mt-8 pl-5">
            <p class="cursor-pointer" @click="back()">
                <i class="fa-solid fa-arrow-left md:fa-xl"></i> <span class="font-bold ml-2"> Back </span>
            </p>
        </div> -->

        <div class="w-full mt-10">
            <Dorm :dorm.sync="dorm" :user.sync="user" ></Dorm>
        </div>
    </div>
</template>

<style>
    .main {
        height: 100%;
        background-color: #E5E8E8;
    }
</style>
