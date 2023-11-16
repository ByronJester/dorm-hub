<script>
import { usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { ref, computed } from 'vue'

export default {
    components: {
        ApplicationLogo,
    },
    setup() {
        const page = usePage();
        const user = page.props.user;
        const helps = page.props.help;
        var dataFaqs = [];

        for (let x = 0; x < helps.length; x++) {
                dataFaqs.push(
                    {
                        answer: helps[x].answer,
                        question: helps[x].question,
                    }
                );
            }
        
           

            const searchQuery = ref("");
            const itemsPerPage = 10; // Set the maximum number of items per page to 10
            const currentPage = ref(1); // Initialize to the first page

            const filteredData = computed(() => {
                const query = searchQuery.value.toLowerCase().trim();
                if (!query) {
                    return dataFaqs; // Return all data if the search query is empty.
                }

                return dataFaqs.filter((row) => {
                    // Modify the conditions as needed for your specific search criteria.
                    return (
                        row.question.toLowerCase().includes(query) 
                    );
                });
            });
            

            const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage));

            const slicedRows = computed(() => {
                const startIndex = (currentPage.value - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                const slicedAndSorted = filteredData.value
                    .slice(startIndex, endIndex)
                    .sort((a, b) => {
                        const dateA = new Date(a.created_at);
                        const dateB = new Date(b.created_at);
                        return dateB - dateA;
                    });

                return slicedAndSorted;
            });

            const hide = ref(Array(slicedRows.length).fill(false));

            const toggle = (rowIndex) => {
                hide.value[rowIndex] = !hide.value[rowIndex];
            };
                
        return {
            page,
            user,
            hide,
            toggle,
            dataFaqs,
            searchQuery,
            itemsPerPage,
            currentPage,
            totalPages,
            slicedRows,
        };
    },
};
</script>
<template>
    <nav
        v-if="
            page.props.user &&
            (page.props.user.user_type === 'tenant' ||
                page.props.user.user_type === 'owner' ||
                page.props.user.user_type === 'admin')
        "
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
                                    :href="route('profile.payment-settings')"
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
                                            v-if="!!notification.redirection"
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

    <nav
        v-else
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
    <div class="w-full flex justify-center items-center">
    <div class="max-w-[1000px] xl:px-20 md:px-10 sm:px-2 px-4">
        <div class="max-w-screen-lg mx-auto">
            <div class="w-full justify-start mt-16">
                <table class="items-center w-full bg-transparent table-fixed">
                    <colgroup>
                        <col style="width: 30%;"> <!-- Adjust the width as needed -->
                        <!-- Add more col elements if you have more columns -->
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="align-middle text-3xl py-3 whitespace-nowrap font-bold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100">
                                <p>Common Questions</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, rowIndex) in slicedRows" :key="rowIndex">
                        <td class="align-middle whitespace-nowrap">
                        <div class="bg-white w-full">
                            <div class="flex flex-row justify-between border-b p-4">
                            <p>{{ item.question }}</p>
                            <i class="fa-solid fa-angle-down cursor-pointer" @click="toggle(rowIndex)"></i>
                            </div>
                            <div class="w-full p-4 bg-gray-100 border-t whitespace-normal" v-show="hide[rowIndex]">
                            {{ item.answer }}
                            </div>
                        </div>
                        </td>
                    </tr>
                    <!-- Add more rows if needed -->
                    </tbody>
                </table>
                <div class="pt-5 border-t border-gray-100 dark:border-slate-800">
                    <div class="block w-full overflow-x-auto">
                        <div class="justify-between items-center block md:flex">
                            <div class="flex items-center justify-start p-4 flex-wrap mb-3">
                                <button type="button" class="text-gray-500 bg-white mr-5 hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                                    Previous
                                </button>
                                <button class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    
</template>
