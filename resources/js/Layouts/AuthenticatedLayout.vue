<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { usePage, router } from '@inertiajs/vue3'

const showingNavigationDropdown = ref(false);

const page = usePage()

const isMobileView = ref(false)
isMobileView.value = screen.width < 600;

const notifications = page.props.notifications

var userPages = [];

if(page.props.auth.user.user_type == 'owner') {
    userPages = [
        {
            label: 'Dorms',
            route: 'owner.dorms'
        },

        {
            label: 'Tenants',
            route: 'owner.tenants.application'
        },
    ]
}

if(page.props.auth.user.user_type == 'admin') {
    userPages = [
        {
            label: 'Dorms',
            route: 'admin.dorms'
        },
        {
            label: 'Tenants',
            route: 'admin.tenants'
        },
    ]
}

if(page.props.auth.user.user_type == 'tenant') {
    userPages = [
        {
            label: 'Dorms',
            route: 'tenant.dorms'
        },
    ]
}

const logOut = () => {
    axios.post(route('logout'), {})
        .then(response => {
            location.reload()
        })
        .catch(error => {
            errors.value = error.response.data.errors
        })
}

const openModal = () => {
    var modal = document.getElementById("notificationModal");

    modal.style.display = "block";

}

const closeModal = () => {
    var modal = document.getElementById("notificationModal");

    modal.style.display = "none";
}

const markAsRead = (id) => {
    router.get(route('notification.mark-as-read', id))
}

const viewNotification = (r) => {
    router.get(route(r))
}

</script>

<template>
    <div>
        <div class="h-full">
            <nav class="bg-white border-b shadow-md border-gray-300">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between nav-bar-h">
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
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route(p.route)" :active="route().current(p.route)"
                                    v-for="p in userPages" :key="p.label" :class="route().current(p.route) ? 'active-bg' : ''"
                                >
                                    <span class="px-3"> {{ p.label }} </span>
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <p class="absolute md:right-12 top-5 cursor-pointer mr-1"
                                    @click="openModal()"
                                >
                                    <i class="fa-solid fa-globe"> </i>
                                </p>

                                <p class="absolute md:right-10 top-3 text-xs cursor-pointer text-red-500 font-bold"
                                    @click="openModal()"
                                    v-if="notifications.filter(x => { return !x.is_read }).length > 0"
                                >
                                    {{ notifications.filter(x => { return !x.is_read }).length }}
                                </p>

                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.first_name  }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('tenant.payments')"> Payments </DropdownLink>
                                        <DropdownLink :href="route('view.user.messages')"> Messages </DropdownLink>
                                        <!-- <DropdownLink :href="route('profile.change.password')"> Change Password </DropdownLink> -->
                                        <DropdownLink as="button" @click="logOut()">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
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
                        <ResponsiveNavLink :href="route(p.route)" :active="route().current(p.route)"
                            v-for="p in userPages" :key="p.label" :class="route().current(p.route) ? 'active-bg' : ''"
                        >
                            <span class="px-3"> {{ p.label }} </span>
                        </ResponsiveNavLink>

                        <div class="w-full">
                            <span class="cursor-pointer ml-7"
                             @click="openModal()"
                            >
                                <i class="fa-solid fa-globe"></i>
                                <span v-if="notifications.filter(x => { return !x.is_read }).length > 0"
                                    class="text-xs text-red-500 absolute"
                                >
                                    {{ notifications.filter(x => { return !x.is_read }).length }}
                                </span>

                            </span>


                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.first_name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('tenant.payments')"> Payments </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('view.user.messages')"> Messages </ResponsiveNavLink>
                            <!-- <ResponsiveNavLink :href="route('profile.change.password')"> Change Password </ResponsiveNavLink> -->
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="w-full">
                <div id="notificationModal" class="notificationModal mt-10 md:mt-0"
                    :style="{'top': isMobileView ? '0px' : '3vw !important'}"
                >
                    <div class="notification-modal-content flex flex-col"
                        :style="{
                            width: isMobileView ? '97%' : '30%',
                            'margin-right': isMobileView ? 'none' : '4vw',
                        }"
                    >
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Notifications
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full bg-gray-300 mt-3 text-center py-5 rounded-md"
                            v-if="notifications.length == 0"
                        >
                            There's no notification.
                        </div>

                        <div class="w-full flex flex-col bg-gray-300 mt-3"
                            style="border-radius: 5px;"
                            v-for="notification in notifications" :key="notification.id"
                        >
                            <div class="px-3 my-3">
                                <p class="text-xs font-bold mt-1">
                                    {{ notification.type }}
                                </p>

                                <p class="text-xs mt-2">
                                    {{ notification.message }}
                                </p>

                                <p class="text-xs mt-5">
                                    <span class="cursor-pointer mr-3" v-if="!!notification.redirection"
                                        @click="viewNotification(notification.redirection)"
                                    >
                                        View
                                    </span>

                                    <span class="cursor-pointer" v-if="!notification.is_read"
                                        @click="markAsRead(notification.id)"
                                    >
                                        Mark as Read
                                    </span>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <main class="bg-white">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
.nav-bar-h {
    height: 8vh;
}



.notificationModal {
    display: none;
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 20px; /* Location of the box */
    left: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: scroll; /* Enable scroll if needed */
}

/* Modal Content */
.notification-modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 100%;
    max-height: 80vh;
    border-radius: 5px;
    overflow: scroll;
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

::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* make scrollbar transparent */
}
</style>
