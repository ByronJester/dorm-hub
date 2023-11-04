<script>
import { usePage, router } from "@inertiajs/vue3";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

export default {
    components: {
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        ApplicationLogo,
    },
    setup(){
        const page = usePage();
        const user = page.props.auth.user;
        const aboutUs = page.props.aboutUs;
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

        console.log(page.props);
      

        return{
            aboutUs,
            user,
            page,
            notifications,
            openModal,
            closeModal,
            markAsRead,
            viewNotification,
            logOut
        }
    }
}

</script>

<template>
    <div>
        <nav v-if="page.props.user && (page.props.user.user_type === 'tenant' || page.props.user.user_type === 'owner' || page.props.user.user_type === 'admin')"
            class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <a href="/" class="ml-2 md:mr-24">
                            <ApplicationLogo />
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
                                    {{$page.props.auth.user ? $page.props.auth.user.first_name: '' }}
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
                                            route('profile.payment-settings')
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

        <nav v-else
            class="fixed top-0 z-50 w-full bg-white shadow-md dark:bg-gray-800 dark:border-gray-700"
        >
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start">
                        <a href="/" class="ml-2 md:mr-24">
                            <ApplicationLogo />
                        </a>
                    </div>
                </div>
            </div>
        </nav>

      
            <img 
            alt="bg-image"
            class="absolute rounded-2xl object-cover w-full h-[400px] bg-center bg-no-repeat inset-0 "
            :src="aboutUs.image_path ?? 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'"
            />
        
        
            <div class="absolute inset-0 h-[400px] rounded-2xl bg-black bg-opacity-50"></div>
            <div
                class="relative mx-auto max-w-screen-xl px-4 py-32 sm:px-6 lg:flex h-[400px]  lg:items-center lg:px-8"
            >
                <div class="max-w-xl text-center ltr:sm:text-left rtl:sm:text-right  ">
                <h1 class="text-3xl font-extrabold sm:text-5xl text-white">
                    {{ aboutUs.title}}

                </h1>

                    <strong class="block text-lg font-extrabold text-orange-400">
                        {{ aboutUs.sub_title }}
                    </strong>
               
                </div>
        </div>


    <!-- Main Content -->
    <div class="container mx-auto py-8">
        <div v-html="aboutUs.content" class="max-w-3xl mx-auto bg-white p-6 rounded-lg">
            
        </div>
    </div>
    <footer
        class="fixed bottom-0 w-full z-20 bg-white rounded-lg border dark:bg-gray-800"
    >
        <div
            class="mx-auto p-4 lg:ml-64 md:flex md:items-center md:justify-between"
        >
            <span
                class="text-sm text-gray-500 sm:text-center dark:text-gray-400"
                >© 2023
                <a href="https://flowbite.com/" class="hover:underline"
                    >Dormhub™</a
                >. All Rights Reserved.</span
            >
            <ul
                class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0"
            >
                <li>
                    <a :href="route('about.us')" class="mr-4 hover:underline md:mr-6"
                        >About us</a
                    >
                </li>
                <li>
                    <a :href="route('privacy.policy')" class="mr-4 hover:underline md:mr-6"
                        >Privacy Policy</a
                    >
                </li>
                <li>
                    <a :href="route('contact.us')" class="hover:underline">Contact us</a>
                </li>
            </ul>
        </div>
    </footer>
    </div>
</template>