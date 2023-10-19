<script>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import { usePage, router } from "@inertiajs/vue3";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue"

export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
    },
    setup() {
        const showSidebar = ref(false);

        const page = usePage();

        const clickShowSideBar = () =>{
            showSidebar.value = !showSidebar.value
        }

        const user = page.props.auth.user;

        const redirectPage = (r) => {
            router.get(route(r));
        };
        const notifications = page.props.notifications;

        const openModal = () => {
            var modal = document.getElementById("notificationModal");

            modal.style.display = "block";
        };

        const closeModal = () => {
            var modal = document.getElementById("notificationModal");

            modal.style.display = "none";
        };

        const markAsRead = (id) => {
            router.get(route("notification.mark-as-read", id));
        };

        const viewNotification = (r) => {
            router.get(route(r));
        };

        const logOut = () => {
            axios
                .post(route("logout"), {})
                .then((response) => {
                    location.reload();
                })
                .catch((error) => {
                    errors.value = error.response.data.errors;
                });
        };
        return {
            showSidebar,
            user,
            notifications,
            redirectPage,
            openModal,
            closeModal,
            clickShowSideBar,
            markAsRead,
            viewNotification,
            logOut,
        };
    },
};
</script>


<template>
    <div>
        <div class="h-full">
            <nav
                class="fixed top-0 z-50 w-full bg-white shadow-md border-gray-200 "
            >
                <div class="py-4 border-b-[1px]">
                    <div class="max-w-[2520px] mx-auto xl:px-20 md:px-10 sm:px-2 px-4">
                        <div class="flex flex-row items-center justify-between gap-3 md:gap-0">
                        <div class="flex items-center justify-start">
                            <a href="/">
                                <ApplicationLogo />
                            </a>   
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center ml-3">
                                <AppDropdown>
                                    <button
                                        type="button"
                                        class="flex text-sm text-black  hover:text-gray-400"
                                        aria-expanded="false"
                                    >
                                        {{ $page.props.auth.user.first_name }}
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            class="ml-1 h-5 w-5 fill-current"
                                        >
                                            <path
                                                d="M15.3 9.3a1 1 0 0 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 1.4-1.4l3.3 3.29 3.3-3.3z"
                                            ></path>
                                        </svg>
                                    </button>

                                    <AppDropdownContent class="">
                                        <AppDropdownItem
                                            :href="route('profile.edit')"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                        >
                                            Profile
                                        </AppDropdownItem>
                                        <AppDropdownItem
                                            :href="route('view.user.messages')"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                        >
                                            Message
                                        </AppDropdownItem>
                                        <AppDropdownItem
                                            v-if="user.user_type == 'owner'"
                                            :href="
                                                route(
                                                    'profile.payment-settings'
                                                )
                                            "
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                            role="menuitem"
                                        >
                                            Payment Setting
                                        </AppDropdownItem>

                                        <AppDropdownItem
                                            v-if="user.user_type == 'tenant'"
                                            @click=""
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                            role="menuitem"
                                        >
                                            Payments
                                        </AppDropdownItem>
                                        <hr />
                                        <AppDropdownItem
                                            @click="logOut()"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                            role="menuitem"
                                        >
                                            Logout
                                        </AppDropdownItem>
                                    </AppDropdownContent>
                                </AppDropdown>
                            </div>

                            <AppDropdown>
                                <button>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-6 h-6"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                                        />
                                    </svg>
                                    <span
                                        class="absolute right-4 cursor-pointer text-white font-bold"
                                        style="font-size: 8px"
                                        v-if="
                                            notifications.filter((x) => {
                                                return !x.is_read;
                                            }).length > 0
                                        "
                                    >
                                        {{
                                            notifications.filter((x) => {
                                                return !x.is_read;
                                            }).length
                                        }}
                                    </span>
                                </button>

                                <AppDropdownContent class="w-100%">
                                    <div
                                        class="block px-4 py-2 w-auto font-medium text-center"
                                    >
                                        Notifications
                                    </div>
                                    <hr />
                                    <div
                                        class="w-ful mt-3 text-center py-5 rounded-md"
                                        v-if="notifications.length == 0"
                                    >
                                        There's no notification.
                                    </div>

                                    <div
                                        class="w-full flex flex-col bg-gray-300 mt-3"
                                        style="border-radius: 5px"
                                        v-for="notification in notifications"
                                        :key="notification.id"
                                    >
                                        <div class="px-3 my-3">
                                            <p class="text-xs font-bold mt-1">
                                                {{ notification.type }}
                                            </p>

                                            <p class="text-xs mt-2">
                                                {{ notification.message }}
                                            </p>

                                            <p class="text-xs mt-5">
                                                <span
                                                    class="cursor-pointer mr-3"
                                                    v-if="
                                                        !!notification.redirection
                                                    "
                                                    @click="
                                                        viewNotification(
                                                            notification.redirection
                                                        )
                                                    "
                                                >
                                                    View
                                                </span>

                                                <span
                                                    class="cursor-pointer"
                                                    v-if="!notification.is_read"
                                                    @click="
                                                        markAsRead(
                                                            notification.id
                                                        )
                                                    "
                                                >
                                                    Mark as Read
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </AppDropdownContent>
                            </AppDropdown>
                        </div>
                    </div>
                    </div>
                </div>
            </nav>

            <main class="bg-white">
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
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
