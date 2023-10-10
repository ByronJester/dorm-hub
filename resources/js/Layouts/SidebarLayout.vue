<script>
    import { usePage, useForm, router, Link } from '@inertiajs/vue3'
    import { ref } from 'vue';
    import Dropdown from '@/Components/Dropdown.vue';
    import DropdownLink from '@/Components/DropdownLink.vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';

    export default {
        components: {
            Dropdown,
            DropdownLink,
            ApplicationLogo
        },
        setup() {
            const page = usePage()

            const user = page.props.auth.user

            const isMobileView = ref(false)
            isMobileView.value = screen.width < 600;

            var routes = [];

            if(user.user_type == 'owner') {

                routes = [
                    {
                        route: 'owner.dashboard',
                        label: 'Dashboard',
                        icon: 'fa-solid fa-chart-line'
                    },
                    {
                        route: 'owner.reports',
                        label: 'Reports',
                        icon: 'fa-solid fa-chart-simple'

                    },
                    {
                        route: 'owner.dorms',
                        label: 'Dormitories',
                        icon: 'fa-solid fa-building'
                    },
                    {
                        route: 'owner.tenants.application',
                        label: 'Tenants',
                        icon: 'fa-solid fa-users'
                    },
                    {
                        route: 'owner.billing',
                        label: 'Billing',
                        icon: 'fa-solid fa-comments-dollar'
                    }
                ]
            }

            const redirectPage = (r) => {
                router.get(route(r))
            }

            const notifications = page.props.notifications

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

            const logOut = () => {
                axios.post(route('logout'), {})
                    .then(response => {
                        location.reload()
                    })
                    .catch(error => {
                        errors.value = error.response.data.errors
                    })
            }
            const isOpenMobileNav = ref(false);

            const openMobileNav = () => {
                isOpenMobileNav.value = !isOpenMobileNav.value
            }

            return {
                user,
                isMobileView,
                routes,
                notifications,
                isOpenMobileNav,
                redirectPage,
                openModal,
                closeModal,
                markAsRead,
                viewNotification,
                logOut,
                openMobileNav
            }
        }
    }

</script>

<template>
    <div class="w-full flex flex-col md:flex-row">
        <div class="w-full md:w-[20%] md:h-screen hidden md:flex flex-row md:flex-col bg-gray-300 fixed"
            style="border-right: 1px solid gray"
        >
            <div class="w-full hidden md:flex flex-col mb-5">
                <div class="w-full flex justify-center items-center">
                    <img :src="'/images/logo.png'"
                        class="mt-5 cursor-pointer pb-2"
                        style="width: 80%; height: 70px; border-bottom: 1px solid black;"
                    >
                </div>
            </div>

            <div class="w-full flex justify-center items-center" v-for="r in routes" :key="r.label">
                <div class="w-full md:w-[80%] flex flex-row py-2 md:py-5 px-5 md:px-10 md:mt-5 cursor-pointer text-xs md:text-md"
                    :class="{ 'text-white': route().current() == r.route, 'bg-gradient-to-r': route().current() == r.route, 'from-sky-600': route().current() == r.route, 'to-cyan-400': route().current() == r.route}"
                    style="border-radius: 15px;"
                    @click="redirectPage(r.route)"
                >
                    <div class="w-[20%]">
                        <i :class="r.icon"></i>
                    </div>

                    <div class="w-[80%]">
                        <p>
                            {{ r.label }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <div class="w-full md:md:w-[100%] h-full min-h-screen md:ml-[20%]">
            <div class="h-[8vh] text-white bg-gradient-to-r from-gray-300 to-orange-500 flex flex-row md:flex-col" v-if="isMobileView">
                <div class="w-[20%] md:hidden mt-3">
                    <span class="cursor-pointer ml-3 text-xl text-black" @click="openMobileNav()">
                        <i class="fa-solid fa-bars" v-if="!isOpenMobileNav"></i>
                        <i class="fa-solid fa-list" v-else></i>
                    </span>
                </div>

                <div class="w-full md:w-[80%] flex justify-end items-center">
                    <p class="relative " @click="openModal()">
                        <i class="fa-solid fa-globe mr-1 cursor-pointer text-xs"></i>
                        <span class="absolute right-4 cursor-pointer text-white font-bold"
                            style="font-size: 8px;"
                            v-if="notifications.filter(x => { return !x.is_read }).length > 0"
                        >
                            {{ notifications.filter(x => { return !x.is_read }).length }}
                        </span>
                    </p>

                    <Dropdown class="mr-3 md:mr-10" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <span
                                    class="inline-flex items-center px-3 py-2 text-xs leading-4 font-medium rounded-md text-white duration-150"
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
                                </span>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                <i class="fa-solid fa-user mr-3"></i> Profile
                            </DropdownLink>
                            <DropdownLink :href="route('view.user.messages')" v-if="user.user_type == 'owner'">
                                <i class="fa-solid fa-message mr-3"></i> Messages
                            </DropdownLink>
                            <DropdownLink as="button" @click="logOut()">
                                <i class="fa-solid fa-right-from-bracket mr-3"></i>Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <div class="h-[8vh] text-white bg-gradient-to-r from-gray-300 to-orange-500 flex justify-end items-center" v-else>

                <p class="relative" @click="openModal()">
                    <i class="fa-solid fa-globe mr-3 cursor-pointer"></i>
                    <span class="absolute right-7 text-xs cursor-pointer text-white font-bold"

                        v-if="notifications.filter(x => { return !x.is_read }).length > 0"
                    >
                        {{ notifications.filter(x => { return !x.is_read }).length }}
                    </span>
                </p>

                <Dropdown class="mr-3 md:mr-10" width="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <span
                                class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-white duration-150"
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
                            </span>
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')">
                            <i class="fa-solid fa-user mr-3"></i> Profile
                        </DropdownLink>
                        <DropdownLink :href="route('view.user.messages')" v-if="user.user_type == 'owner'">
                            <i class="fa-solid fa-message mr-3"></i> Messages
                        </DropdownLink>
                        <DropdownLink as="button" @click="logOut()">
                            <i class="fa-solid fa-right-from-bracket mr-3"></i>Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>

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

            <div class="w-full flex flex-col"
                style="border-top: 1px solid gray; border-bottom: 1px solid gray; background-color: #E5E8E8;"
                v-if="isOpenMobileNav"
            >
                <div class="w-full flex flex-row" v-for="r in routes" :key="r.label"
                    :class="{ 'text-white': route().current() == r.route, 'bg-gradient-to-r': route().current() == r.route, 'from-sky-600': route().current() == r.route, 'to-cyan-400': route().current() == r.route}"
                    @click="redirectPage(r.route)"
                >
                    <div class="w-[10%]">
                        <i :class="r.icon" class="ml-1"></i>
                    </div>

                    <div class="w-[90%]">
                        <span class="ml-1"> {{ r.label }}</span>
                    </div>
                </div>
            </div>

            <slot />
        </div>
    </div>
</template>

<style scoped>
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
