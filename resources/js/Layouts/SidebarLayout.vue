<script>
import { usePage, router } from "@inertiajs/vue3";
import { ref } from "vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";

export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
    },
    setup() {
        const showSidebar = ref(false);

        const page = usePage();

        const clickShowSideBar = () =>{
            showSidebar.value = !showSidebar.value
        }

        const user = page.props.auth.user;

        var routes = [];

        if (user.user_type == "owner") {
            routes = [
                {
                    route: "owner.dashboard",
                    label: "Dashboard",
                    icon: "fa-solid fa-chart-line",
                },
                {
                    route: "#",
                    label: "Reports",
                    icon: "fa-solid fa-chart-simple",
                },
                {
                    route: "owner.dorms",
                    label: "Dormitories",
                    icon: "fa-solid fa-building",
                },
                {
                    route: "#",
                    label: "Tenants",
                    icon: "fa-solid fa-users",
                },
                {
                    route: "#",
                    label: "Billing",
                    icon: "fa-solid fa-comments-dollar",
                },
            ];
        }
        if (user.user_type == "admin") {
            routes = [
                {
                    route: "#",
                    label: "Dashboard",
                    icon: "fa-solid fa-chart-line",
                },
                {
                    route: "admin.dorms",
                    label: "Dorm Verificartion",
                    icon: "fa-solid fa-chart-simple",
                },
                {
                    route: "admin.tenants",
                    label: "User Verification",
                    icon: "fa-solid fa-building",
                },
                {
                    route: "#",
                    label: "Reports",
                    icon: "fa-solid fa-users",
                },
                {
                    route: "#",
                    label: "Utilities",
                    icon: "fa-solid fa-comments-dollar",
                },
            ];
        }

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
            routes,
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
    <nav
        class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
    >
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button
                        @click="clickShowSideBar()"
                        class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-100 dark:focus:ring-gray-600"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg
                            class="w-6 h-6"
                            aria-hidden="true"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
                            ></path>
                        </svg>
                    </button>
                    <a href="#" class="flex ml-2 md:mr-24">
                        <span
                            class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white"
                            >Dormhub</span
                        >
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ml-3">
                        <AppDropdown>
                            <button
                                type="button"
                                class="flex text-sm text-black dark:text-white hover:text-gray-400"
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
                                    class="block px-4 py-2 text-sm  text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                >
                                        Message
                                </AppDropdownItem>
                                <AppDropdownItem
                                    v-if = "user.user_type == 'owner'"
                                    :href="route('profile.payment-settings')"
                                    class="block px-4 py-2 text-sm  text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                    role="menuitem"
                                >
                                        Payment Setting
                                </AppDropdownItem>
                                
                                <AppDropdownItem
                                    v-if = "user.user_type == 'tenant'"
                                    @click=""
                                    class="block px-4 py-2 text-sm  text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
                                    role="menuitem"
                                >
                                        Payments
                                </AppDropdownItem>
                                <hr />
                                <AppDropdownItem
                                    @click="logOut()"
                                    class="block px-4 py-2 text-sm  text-gray-700 hover:bg-orange-400 hover:text-white dark:text-black dark:hover:bg-orange-400 dark:hover:text-white"
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
                            class="w-6 h-6 dark:text-white"
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

                    <AppDropdownContent class="w-80 p-5 h-auto">
                        <div class="block px-4 py-2 w-auto font-medium text-center">
                            Notifications
                        </div>
                        <hr/>
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
                                v-if="!!notification.redirection"
                                @click="
                                    viewNotification(notification.redirection)
                                "
                            >
                                View
                            </span>

                            <span
                                class="cursor-pointer"
                                v-if="!notification.is_read"
                                @click="markAsRead(notification.id)"
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
    </nav>

    <div
        id="sidebarModal"
        :class="{ 'translate-x-0': showSidebar, '-translate-x-full': !showSidebar }"
        class="fixed top-0 left-0 z-40 w-64 shadow-md h-screen pt-20 transition-transform bg-white  -translate-x-full dark:bg-gray-800 lg:-translate-x-0 dark:border-gray-700"
    >
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <div
                class="w-full flex justify-center items-center"
                v-for="r in routes"
                :key="r.label"
            >
                <div
                    class="w-full md:w-[80%] flex text-black hover:bg-orange-400 hover:text-white dark:text-white flex-row py-2 md:py-5 px-5 md:px-10 md:mt-5 cursor-pointer text-xs md:text-md"
                    :class="{
                        'text-white': route().current() == r.route,
                        'bg-gradient-to-r': route().current() == r.route,
                        'from-orange-400': route().current() == r.route,
                        'to-red-600': route().current() == r.route,
                    }"
                    style="border-radius: 10px"
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
    </div>



    <slot />
</template>
<style scoped>

</style>
