<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);

const isMobileView = ref(false)

isMobileView.value = screen.width < 600;

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

</script>

<script>
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import { Link } from '@inertiajs/vue3';


    export default {
        components: {
            ApplicationLogo,
            Link
        },
        methods: {
            openRegisterModal(){
                var modal = document.getElementById("registerModal");

                modal.style.display = "block";
            },

            closeRegisterModal(){
                var modal = document.getElementById("registerModal");

                modal.style.display = "none";
            }
        }
    }
</script>

<template>
    <div>
        <div class="h-full">
            <nav class="bg-white border-b border-gray-300 py-4">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between nav-bar-h relative">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex absolute right-0">
                                <span
                                    class="ml-4 font-semibold text-black focus:outline focus:outline-2 focus:rounded-sm cursor-pointer mt-3"
                                >

                                    <Link
                                        :href="route('login')"
                                        class="font-semibold text-black"
                                        >LOGIN</Link
                                    >
                                </span
                                >

                                <span
                                    v-if="canRegister"
                                    @click="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-3 cursor-pointer"
                                    >SIGN UP</span
                                >
                            </div>
                        </div>



                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <div class="flex flex-col">
                            <div class="w-full mt-2">
                                <span
                                    class="ml-4 font-semibold text-black focus:outline focus:outline-2 focus:rounded-sm cursor-pointer mt-3"
                                >
                                    <Link
                                        :href="route('login')"
                                        class="font-semibold text-black"
                                        >LOGIN</Link
                                    >
                                </span>
                            </div>

                            <div class="w-full mt-2">
                                <span
                                    @click="openRegisterModal()"
                                    class="ml-4 font-semibold text-black mt-3"
                                    >SIGN UP</span
                                >
                            </div>
                        </div>
                    </div>

                </div>

            </nav>

            <div class="w-full">
                <div id="registerModal" class="registerModal mt-40 md:mt-0">
                    <div class="register-modal-content flex flex-col" :style="{width: isMobileView ? '90%' : '40%'}">
                        <div class="w-full">
                            <span class="text-2xl font-bold ">
                                Sign up account as:
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeRegisterModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full flex flex-row mt-20">
                            <div class="w-full center">
                                <Link
                                    :href="route('tenant.register')"
                                    class="font-semibold text-white register-btn rounded-sm text-lg py-3 text-center"
                                    >Tenant</Link
                                >
                            </div>

                            <div class="w-full center">
                                <Link
                                    :href="route('owner.register')"
                                    class="font-semibold text-white register-btn rounded-sm text-lg py-3 text-center"
                                    >Dorm Owner</Link
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- <div class="w-full">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 text-right p-4">
            <Link
                v-if="$page.props.auth.user"
                :href="route('login')"
                class="font-semibold text-black focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                ></Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-black dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm"
                    >Sign In</Link
                >

                <span
                    v-if="canRegister"
                    @click="openRegisterModal()"
                    class="ml-4 font-semibold text-black dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm cursor-pointer"
                    >Sign Up</span
                >
            </template>
        </div>

        <div class="flex flex-col bg-landing-page h-screen p-10">
            <div class="w-full">
                123
            </div>
        </div>

        <div id="registerModal" class="registerModal">
            <div class="register-modal-content flex flex-col" style="width: 40%">
                <div class="w-full">
                    <span class="text-2xl font-bold ">
                        Sign up account as:
                    </span>
                    <span class="float-right cursor-pointer"
                        @click="closeRegisterModal()"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </div>

                <div class="w-full flex flex-row mt-20">
                    <div class="w-full center">
                        <Link
                            :href="route('tenant.register')"
                            class="font-semibold text-white register-btn rounded-sm text-lg py-3 text-center"
                            >Tenant</Link
                        >
                    </div>

                    <div class="w-full center">
                        <Link
                            :href="route('owner.register')"
                            class="font-semibold text-white register-btn rounded-sm text-lg py-3 text-center"
                            >Dorm Owner</Link
                        >
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</template>

<style>
.bg-landing-page {
    background-color: #EB984E;
}

.register-btn {
    background-color: #5499C7;
    width: 150px;
}

.center {
    display: flex;
    justify-content: center;
    align-items: center;
}

.registerModal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 20px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.register-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 100%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
