<script>
import { usePage, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Image from "primevue/image";


export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
        Image,
        Link
    },
    setup() {
        const showSidebar = ref(false);
        const isDropDownOpen = ref(false);

        const toggleDropDown = () => {
            isDropDownOpen.value = !isDropDownOpen.value;
        };

        const page = usePage();

        const clickShowSideBar = () => {
            showSidebar.value = !showSidebar.value;
        };

        const user = page.props.auth.user;

        var routes = [];

        if (user.user_type == "owner") {
            routes = [
                {
                    route: "owner.dashboard",
                },
                {
                    route: "owner.dorms",
                },
                {
                    route: "owner.manage.tenants",
                },
                {
                    route: "owner.manage.application",
                },
                {
                    route: "#",
                },
                {
                    route: "#",
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
            isDropDownOpen,
            routes,
            notifications,
            redirectPage,
            toggleDropDown,
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
        <nav
            class="fixed top-0 z-40 w-full bg-white shadow-md "
        >
            <div class="px-3 py-5 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between mx-10">
                    <div class="flex items-center justify-start">
                        <button
                            @click="clickShowSideBar()"
                            class="inline-flex items-center p-2 text-sm  rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 "
                        >
                            <span class="sr-only">Open sidebar</span>
                            <i class="fa-solid fa-bars fa-xl"></i>
                        </button>
                        <Link href="/" class="ml-2 md:mr-24">
                            <ApplicationLogo />
                        </Link>
                    </div>
                    <div class="flex flex-row gap-6 items-center text-gray-700">
                        <a :href="route('view.user.messages')" class="inline-flex p-3 items-center text-sm cursor-pointer rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 ">
                            <i class="fa-regular fa-comments fa-lg"></i>
                        </a>
                        
                        <AppDropdown >
                            <button>
                                <i class="fa-regular fa-bell fa-lg"></i>
                                <span
                                    class="absolute right-2 top-2 cursor-pointer text-xs bg-red-400 rounded-full py-1 px-2 text-white font-bold"
                                    
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
                                    class="w-full flex flex-col bg-gray-300 mt-3 overflow-y-scroll"
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
                                                    markAsRead(notification.id)
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
        </nav>

        <div
            id="sidebarModal"
            :class="{
                'translate-x-0': showSidebar,
                '-translate-x-full': !showSidebar,
            }"
            class="fixed top-0 left-0 z-30 w-64 shadow-lg h-screen overflow-auto 2xl:overflow-clip pt-16 transition-transform bg-white -translate-x-full lg:-translate-x-0 "
        >
        <div class="p-3 w-full flex flex-col items-center justify-center">
            <div class="w-full"> 
                <Link @click="logOut()"  class="cursor-pointer float-right hover:text-gray-400 ">
                        <i v-tooltip.bottom="'Logout'" class="fa-solid fa-right-from-bracket"></i>
                </Link>
            </div>
            <img
                :src="user.image ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                alt="Profile picture"
                class="rounded-full block md:h-28 w-28 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 "
            />
            <p class="mt-2 text-gray-700 font-bold text-lg">
                {{  user.first_name }}
            </p>
            <p class="mt-1 text-gray-400">
                {{  user.user_type }}
            </p>
            <Link :href="route('profile.edit')" class="py-2 px-3 mt-3 bg-orange-400 text-white rounded-lg">
                Manage Profile
            </Link>
            
        </div>
        <hr class="mt-3 mx-5"/>
            <div class="h-full overflow-y-auto p-5 bg-white">
                <ul
                    class="space-y-5 font-medium"
                    v-if="user.user_type == 'admin'"
                >
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'admin.dashboard',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.dashboard',
                                'from-orange-400':
                                    route().current() == 'admin.dashboard',
                                'to-red-600':
                                    route().current() == 'admin.dashboard',
                            }"
                            :href="route('admin.dashboard')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-solid fa-house"></i>
                            <span class="ml-1">Dashboard</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'admin.dorms',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.dorms',
                                'from-orange-400':
                                    route().current() == 'admin.dorms',
                                'to-red-600':
                                    route().current() == 'admin.dorms',
                            }"
                            :href="route('admin.dorms')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-solid fa-building fa-md"></i>
                            <span class="ml-1">Dorm Verification</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'admin.tenants',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.tenants',
                                'from-orange-400':
                                    route().current() == 'admin.tenants',
                                'to-red-600':
                                    route().current() == 'admin.tenants',
                            }"
                            :href="route('admin.tenants')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-solid fa-user fa-md"></i>
                            <span class="ml-1">User Verification</span>
                        </Link>
                    </li>

                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'admin.reports',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.reports',
                                'from-orange-400':
                                    route().current() == 'admin.reports',
                                'to-red-600':
                                    route().current() == 'admin.reports',
                            }"
                            :href="route('admin.reports')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-solid fa-file-export"></i>
                            <span class="ml-1">Report</span>
                        </Link>
                    </li>

                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'admin.maintenance',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.maintenance',
                                'from-orange-400':
                                    route().current() == 'admin.maintenance',
                                'to-red-600':
                                    route().current() == 'admin.maintenance',
                            }"
                            :href="route('admin.maintenance')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-regular fa-window-restore"></i>
                            <span class="ml-1">Site Maintenance</span>
                        </Link>
                    </li>

                    <li>
                        <button
                            type="button"
                            class="flex items-center w-full text-base text-gray-900 transition duration-75 rounded-lg group "
                            aria-controls="dropdown-example"
                            @click="toggleDropDown()"
                        >
                            <i class="fa-solid fa-gear"></i>
                            <span
                                class="flex-1 ml-1 text-left whitespace-nowrap"
                                >Utilities</span
                            >
                            <i
                                class="block fa-solid fa-caret-left cursor-pointer"
                                :class="{ hidden: isDropDownOpen }"
                            ></i>
                            <i class="fa-solid fa-caret-down block" :class="{ hidden: !isDropDownOpen }"></i>
                        </button>
                        <ul id="dropdown-example" class="block my-3 ml-5" :class="{ hidden: !isDropDownOpen }">
                            <li>
                                <Link
                                :class="{
                                'text-white':
                                    route().current() == 'admin.addUser',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.addUser',
                                'from-orange-400':
                                    route().current() == 'admin.addUser',
                                'to-red-600':
                                    route().current() == 'admin.addUser',
                            }"
                            :href="route('admin.addUser')"
                            class="flex items-center p-2 mb-1 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                                >
                                    Add User
                                </Link>
                            </li>
                            <li>
                                <Link
                                :class="{
                                'text-white':
                                    route().current() == 'admin.backUp',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.backUp',
                                'from-orange-400':
                                    route().current() == 'admin.backUp',
                                'to-red-600':
                                    route().current() == 'admin.backUp',
                            }"
                            :href="route('admin.backUp')"
                            class="flex items-center p-2 mb-1 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                                >
                                    BackUp and Restore
                                </Link>
                            </li>
                            <li>
                                <Link
                                :class="{
                                'text-white':
                                    route().current() == 'admin.archive',
                                'bg-gradient-to-r':
                                    route().current() == 'admin.archive',
                                'from-orange-400':
                                    route().current() == 'admin.archive',
                                'to-red-600':
                                    route().current() == 'admin.archive',
                            }"
                            :href="route('admin.archive')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                                 >
                                    Archive
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul
                    class="space-y-2 font-bold"
                    v-if="user.user_type == 'owner'"
                >
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.dashboard',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.dashboard',
                                'from-orange-400':
                                    route().current() == 'owner.dashboard',
                                'to-red-600':
                                    route().current() == 'owner.dashboard',
                            }"
                            :href="route('owner.dashboard')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor"
                                viewBox="0 0 22 21"
                            >
                                <path
                                    d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"
                                />
                                <path
                                    d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"
                                />
                            </svg>
                            <span class="ml-1">Dashboard</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.dorms',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.dorms',
                                'from-orange-400':
                                    route().current() == 'owner.dorms',
                                'to-red-600':
                                    route().current() == 'owner.dorms',
                            }"
                            :href="route('owner.dorms')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"
                                />
                            </svg>
                            <span class="ml-1">Manage Dormitories</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.manage.tenants',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.manage.tenants',
                                'from-orange-400':
                                    route().current() == 'owner.manage.tenants',
                                'to-red-600':
                                    route().current() == 'owner.manage.tenants',
                            }"
                            :href="route('owner.manage.tenants')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"
                                />
                            </svg>
                            <span class="ml-1">Manage Tenants</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() ==
                                    'owner.manage.application',
                                'bg-gradient-to-r':
                                    route().current() ==
                                    'owner.manage.application',
                                'from-orange-400':
                                    route().current() ==
                                    'owner.manage.application',
                                'to-red-600':
                                    route().current() ==
                                    'owner.manage.application',
                            }"
                            :href="route('owner.manage.application')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H544c53 0 96-43 96-96V96c0-53-43-96-96-96H96zM64 96c0-17.7 14.3-32 32-32H544c17.7 0 32 14.3 32 32V416c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V96zm159.8 80a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM96 309.3c0 14.7 11.9 26.7 26.7 26.7h56.1c8-34.1 32.8-61.7 65.2-73.6c-7.5-4.1-16.2-6.4-25.3-6.4H149.3C119.9 256 96 279.9 96 309.3zM461.2 336h56.1c14.7 0 26.7-11.9 26.7-26.7c0-29.5-23.9-53.3-53.3-53.3H421.3c-9.2 0-17.8 2.3-25.3 6.4c32.4 11.9 57.2 39.5 65.2 73.6zM372 289c-3.9-.7-7.9-1-12-1H280c-4.1 0-8.1 .3-12 1c-26 4.4-47.3 22.7-55.9 47c-2.7 7.5-4.1 15.6-4.1 24c0 13.3 10.7 24 24 24H408c13.3 0 24-10.7 24-24c0-8.4-1.4-16.5-4.1-24c-8.6-24.3-29.9-42.6-55.9-47zM512 176a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zM320 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128z"
                                />
                            </svg>
                            <span class="ml-1">Applicants</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() ==
                                    'owner.manage.reservation',
                                'bg-gradient-to-r':
                                    route().current() ==
                                    'owner.manage.reservation',
                                'from-orange-400':
                                    route().current() ==
                                    'owner.manage.reservation',
                                'to-red-600':
                                    route().current() ==
                                    'owner.manage.reservation',
                            }"
                            :href="route('owner.manage.reservation')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                        <svg class=" ml-2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="30" height="25" viewBox="0 0 256 256" xml:space="preserve">     
                                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10;  opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)">
                                        <path d="M 83.247 13.625 h -9.953 V 9.819 c 0 -2.105 -1.713 -3.818 -3.818 -3.818 h -3.997 c -2.104 0 -3.817 1.713 -3.817 3.818 v 3.806 H 50.816 V 9.819 c 0 -2.105 -1.713 -3.818 -3.817 -3.818 h -3.998 c -2.105 0 -3.818 1.713 -3.818 3.818 v 3.806 H 28.339 V 9.819 c 0 -2.105 -1.713 -3.818 -3.818 -3.818 h -3.997 c -2.105 0 -3.818 1.713 -3.818 3.818 v 3.806 H 6.753 C 3.029 13.625 0 16.654 0 20.378 v 56.868 c 0 3.724 3.029 6.753 6.753 6.753 h 76.494 c 3.724 0 6.753 -3.029 6.753 -6.753 V 20.378 C 90 16.654 86.971 13.625 83.247 13.625 z M 83.247 79.999 H 6.753 C 5.235 79.999 4 78.764 4 77.246 V 31.472 h 82 v 45.774 C 86 78.764 84.765 79.999 83.247 79.999 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 22.81 36.583 h -8.392 c -1.973 0 -3.579 1.605 -3.579 3.579 v 8.392 c 0 1.974 1.605 3.579 3.579 3.579 h 8.392 c 1.973 0 3.579 -1.605 3.579 -3.579 v -8.392 C 26.388 38.189 24.783 36.583 22.81 36.583 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 49.196 36.583 h -8.392 c -1.973 0 -3.579 1.605 -3.579 3.579 v 8.392 c 0 1.974 1.605 3.579 3.579 3.579 h 8.392 c 1.973 0 3.578 -1.605 3.578 -3.579 v -8.392 C 52.774 38.189 51.169 36.583 49.196 36.583 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 75.582 36.583 H 67.19 c -1.973 0 -3.578 1.605 -3.578 3.579 v 8.392 c 0 1.974 1.605 3.579 3.578 3.579 h 8.392 c 1.974 0 3.579 -1.605 3.579 -3.579 v -8.392 C 79.161 38.189 77.556 36.583 75.582 36.583 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 22.81 59.061 h -8.392 c -1.973 0 -3.579 1.605 -3.579 3.579 v 8.392 c 0 1.973 1.605 3.578 3.579 3.578 h 8.392 c 1.973 0 3.579 -1.605 3.579 -3.578 V 62.64 C 26.388 60.666 24.783 59.061 22.81 59.061 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 75.582 59.061 H 67.19 c -1.973 0 -3.578 1.605 -3.578 3.579 v 8.392 c 0 1.973 1.605 3.578 3.578 3.578 h 8.392 c 1.974 0 3.579 -1.605 3.579 -3.578 V 62.64 C 79.161 60.666 77.556 59.061 75.582 59.061 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                        <path d="M 51.272 62.67 c -0.908 -0.631 -2.153 -0.408 -2.784 0.499 l -4.409 6.336 l -2.459 -2.458 c -0.781 -0.781 -2.047 -0.781 -2.829 0 c -0.781 0.781 -0.781 2.048 0 2.828 l 4.149 4.148 c 0.377 0.377 0.886 0.586 1.414 0.586 c 0.059 0 0.118 -0.003 0.177 -0.008 c 0.59 -0.053 1.126 -0.363 1.464 -0.85 l 5.774 -8.298 C 52.402 64.547 52.179 63.301 51.272 62.67 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round"/>
                                    </g>
                                    </svg>
                            <span class="ml-1">Reservation</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.billing',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.billing',
                                'from-orange-400':
                                    route().current() == 'owner.billing',
                                'to-red-600':
                                    route().current() == 'owner.billing',
                            }"
                            :href="route('owner.billing')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M416 176c0 97.2-93.1 176-208 176c-38.2 0-73.9-8.7-104.7-23.9c-7.5 4-16 7.9-25.2 11.4C59.8 346.4 37.8 352 16 352c-6.9 0-13.1-4.5-15.2-11.1s.2-13.8 5.8-17.9l0 0 0 0 .2-.2c.2-.2 .6-.4 1.1-.8c1-.8 2.5-2 4.3-3.7c3.6-3.3 8.5-8.1 13.3-14.3c5.5-7 10.7-15.4 14.2-24.7C14.7 250.3 0 214.6 0 176C0 78.8 93.1 0 208 0S416 78.8 416 176zM231.5 383C348.9 372.9 448 288.3 448 176c0-5.2-.2-10.4-.6-15.5C555.1 167.1 640 243.2 640 336c0 38.6-14.7 74.3-39.6 103.4c3.5 9.4 8.7 17.7 14.2 24.7c4.8 6.2 9.7 11 13.3 14.3c1.8 1.6 3.3 2.9 4.3 3.7c.5 .4 .9 .7 1.1 .8l.2 .2 0 0 0 0c5.6 4.1 7.9 11.3 5.8 17.9c-2.1 6.6-8.3 11.1-15.2 11.1c-21.8 0-43.8-5.6-62.1-12.5c-9.2-3.5-17.8-7.4-25.2-11.4C505.9 503.3 470.2 512 432 512c-95.6 0-176.2-54.6-200.5-129zM228 72c0-11-9-20-20-20s-20 9-20 20V86c-7.6 1.7-15.2 4.4-22.2 8.5c-13.9 8.3-25.9 22.8-25.8 43.9c.1 20.3 12 33.1 24.7 40.7c11 6.6 24.7 10.8 35.6 14l1.7 .5c12.6 3.8 21.8 6.8 28 10.7c5.1 3.2 5.8 5.4 5.9 8.2c.1 5-1.8 8-5.9 10.5c-5 3.1-12.9 5-21.4 4.7c-11.1-.4-21.5-3.9-35.1-8.5c-2.3-.8-4.7-1.6-7.2-2.4c-10.5-3.5-21.8 2.2-25.3 12.6s2.2 21.8 12.6 25.3c1.9 .6 4 1.3 6.1 2.1l0 0 0 0c8.3 2.9 17.9 6.2 28.2 8.4V280c0 11 9 20 20 20s20-9 20-20V266.2c8-1.7 16-4.5 23.2-9c14.3-8.9 25.1-24.1 24.8-45c-.3-20.3-11.7-33.4-24.6-41.6c-11.5-7.2-25.9-11.6-37.1-15l-.7-.2c-12.8-3.9-21.9-6.7-28.3-10.5c-5.2-3.1-5.3-4.9-5.3-6.7c0-3.7 1.4-6.5 6.2-9.3c5.4-3.2 13.6-5.1 21.5-5c9.6 .1 20.2 2.2 31.2 5.2c10.7 2.8 21.6-3.5 24.5-14.2s-3.5-21.6-14.2-24.5c-6.5-1.7-13.7-3.4-21.1-4.7V72z"
                                />
                            </svg>
                            <span class="ml-1">Billing</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.maintenance',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.maintenance',
                                'from-orange-400':
                                    route().current() == 'owner.maintenance',
                                'to-red-600':
                                    route().current() == 'owner.maintenance',
                            }"
                            :href="route('owner.maintenance')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1zM512 144c0-10.5-1.1-20.7-3.2-30.5c-2.4-11.2-16.1-14.1-24.2-6l-63.9 63.9c-3 3-7.1 4.7-11.3 4.7H352c-8.8 0-16-7.2-16-16V102.6c0-4.2 1.7-8.3 4.7-11.3l63.9-63.9c8.1-8.1 5.2-21.8-6-24.2C388.7 1.1 378.5 0 368 0C288.5 0 224 64.5 224 144l0 .8 85.3 85.3c36-9.1 75.8 .5 104 28.7L429 274.5c49-23 83-72.8 83-130.5zM56 432a24 24 0 1 1 48 0 24 24 0 1 1 -48 0z"
                                />
                            </svg>
                            <span class="ml-1">Requests</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                            :class="{
                                'text-white':
                                    route().current() == 'owner.reports',
                                'bg-gradient-to-r':
                                    route().current() == 'owner.reports',
                                'from-orange-400':
                                    route().current() == 'owner.reports',
                                'to-red-600':
                                    route().current() == 'owner.reports',
                            }"
                            :href="route('owner.reports')"
                            class="flex items-center p-2 text-gray-900  hover:bg-orange-400 hover:text-white group"
                        >
                            <svg
                                class="w-10 h-5  transition duration-75  group-hover:text-gray-900 "
                                viewBox="0 0 384 512"
                                fill="currentColor"
                            >
                                <path
                                    d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V288H216c-13.3 0-24 10.7-24 24s10.7 24 24 24H384V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zM384 336V288H494.1l-39-39c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l80 80c9.4 9.4 9.4 24.6 0 33.9l-80 80c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l39-39H384zm0-208H256V0L384 128z"
                                />
                            </svg>
                            <span class="ml-1">Reports</span>
                        </Link>
                    </li>
                </ul>
                <ul 
                    class="space-y-5 font-medium"
                    v-if="user.user_type == 'tenant'"
                >
                <li>
                        <Link
                            :class="{
                                'text-white bg-gradient-to-r from-orange-400 to-red-600': route().current() == 'tenant.dorms'
                            }"
                            :href="route('tenant.dorms')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-solid fa-house"></i>
                            <span class="ml-1">Home</span>
                        </Link>
                    </li>
                    <li>
                            <Link
                                :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600': route().current() == 'tenant.mydorm'
                                }"
                                :href="route('tenant.mydorm')"
                                class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                            >
                                <i class="fa-solid fa-building-user" style="color: #000000;"></i>
                                <span class="ml-1">My Dorm</span>
                            </Link>
                    </li>
                    <li>
                        <Link
                        :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600': route().current() == 'tenant.reservation'
                                }"
                            :href="route('tenant.reservation')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-regular fa-calendar" style="color: #000000;"></i>
                            <span class="ml-1">My Reservation</span>
                        </Link>
                    </li>
                    <li>
                        <Link
                        :class="{
                                    'text-white bg-gradient-to-r from-orange-400 to-red-600': route().current() == 'tenant.payements'
                                }"
                            :href="route('tenant.payments')"
                            class="flex items-center p-2 text-gray-900 rounded-lg  hover:bg-orange-400 hover:text-white group"
                        >
                            <i class="fa-regular fa-credit-card" style="color: #000000;"></i>
                            <span class="ml-1">Payment</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
        <main class="flex-grow mb-16 sm:mb-24">
            <slot />
        </main>
    </div>

    <footer
        class="fixed bottom-0 w-full z-20 bg-white rounded-lg border"
    >
        <div
            class="mx-auto p-4 lg:ml-64 md:flex md:items-center md:justify-between"
        >
            <span
                class="text-sm  sm:text-center "
                >© 2023
                <Link href="https://dormhub.onrender.com/" class="hover:underline"
                    >Dormhub™</Link
                >. All Rights Reserved.</span
            >
            <ul
                class="flex flex-wrap items-center mt-3 gap-4 text-sm font-medium   sm:mt-0"
            >
                <li>
                    <Link :href="route('about.us')" class="hover:underline md:mr-6"
                        >About us</Link
                    >
                </li>
                <li>
                    <Link :href="route('privacy.policy')" class="hover:underline md:mr-6"
                        >Privacy Policy</Link
                    >
                </li>
                <li>
                    <Link :href="route('contact.us')" class="hover:underline">Contact us</Link>
                </li>
                <li>
                    <Link :href="route('user.help')" class=" hover:underline md:mr-6"
                        >Help</Link
                    >
                </li>
            </ul>
        </div>
    </footer>
</template>
<style></style>
