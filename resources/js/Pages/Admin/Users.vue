<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import 'vue-good-table-next/dist/vue-good-table-next.css'
    import { VueGoodTable } from 'vue-good-table-next';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';

    export default {
        components: {
            AuthenticatedLayout,
            VueGoodTable
        },
        setup() {
            const isMobileView = ref(false)
            const page = usePage()
            const rows = ref([])
            const columns = ref([
                {
                    label: 'Name',
                    field: 'name',
                },
                {
                    label: 'Contact Number',
                    field: 'phone_number',
                },
                {
                    label: 'User Type',
                    field: 'user_type',
                },
                {
                    label: 'Username',
                    field: 'username',
                },
                {
                    label: 'Status',
                    field: 'status',
                },
                {
                    label: 'Actions',
                    field: 'action',
                },

            ])

            onMounted(() => {
                rows.value = page.props.users
            });

            const openModal = (arg) => {
                id_picture.value = arg.id_picture
                user.value = arg

                var modal = document.getElementById("userModal");

                modal.style.display = "block";

            }

            const closeModal = () => {
                var modal = document.getElementById("userModal");

                modal.style.display = "none";
            }

            const changeStatus = (id, status) => {
                swal({
                    title: `Are you sure to ${!status ? 'deny' : 'approve'} the rental status of this tenant?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('tenant.change.status', status), {id: id, status: status})
                        .then(response => {
                            swal("Success!", `You successfully ${!status ? 'denied' : 'approved'} the rental rental status of this tenant.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });


            }
        const currentPage = ref(1); // Initialize to the first page
        const itemsPerPage = 10;
        const totalPages = computed(() => Math.ceil(rows.value.length / itemsPerPage));

        const slicedRows = computed(() => {
        const startIndex = (currentPage.value - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return filteredRows.value.slice(startIndex, endIndex);
        });

        const changePage = (pageChange) => {
            const newPage = currentPage.value + pageChange;
            if (newPage >= 1 && newPage <= totalPages.value) {
                currentPage.value = newPage;
            }
        };

        //search function
        const searchQuery = ref('');
        const filteredRows = computed(() => {
        const query = searchQuery.value?.toLowerCase().trim();
        if (!query) {
            return rows.value; // Return all rows if the search query is empty.
        }

        return rows.value.filter(row => {
            // Modify the conditions as needed for your specific search criteria.
            return (
            row.name.toLowerCase().includes(query)||
            row.status.toLowerCase().includes(query)
            // Add more conditions for other columns as needed
            );
        });
        });

        const id_picture = ref(null)
        const selfie_id_picture = ref(null)
        const user = ref(null)
        const openTermsModal = (arg) => {
            console.log(arg)
            id_picture.value = arg.id_picture
            selfie_id_picture.value = arg.selfie_id_picture
            user.value = arg

            var modal = document.getElementById("defaultModal");

            modal.style.display = "block";
        };

        const closeTermsModal = () => {
            var modal = document.getElementById("defaultModal");

            modal.style.display = "none";
        };



            return {
                slicedRows,
                searchQuery,
                changePage,
                itemsPerPage,
                currentPage,
                isMobileView,
                columns,
                rows,
                id_picture,
                user,
                openModal,
                closeModal,
                changeStatus,
                openTermsModal,
                closeTermsModal,
                selfie_id_picture
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 lg:ml-64">
            <div class="flex items-center justify-start mt-16">
                <span
                    class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3"
                >
                        <svg
                            class="w-10 h-10 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            viewBox="0 0 384 512"
                            fill="currentColor"
                        >
                            <path
                                d="M48 0C21.5 0 0 21.5 0 48V464c0 26.5 21.5 48 48 48h96V432c0-26.5 21.5-48 48-48s48 21.5 48 48v80h96c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48H48zM64 240c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V240zm112-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V240c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V240zM80 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16zm80 16c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H176c-8.8 0-16-7.2-16-16V112zM272 96h32c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V112c0-8.8 7.2-16 16-16z"
                            />
                        </svg>
                </span>
                <h3 class="text-3xl">User Verification</h3>
            </div>
            <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return (
                                        !!x.is_approved &&
                                        !x.is_approved
                                    );
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Users</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return !!x.is_approved;
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Approved Users</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{
                                rows.filter((x) => {
                                    return !x.is_approved;
                                }).length
                            }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Pending Users</p>
                    </div>
                </div>
            </div>
            <div class="w-full mb-5 mt-5">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded bg-white border"
                >
                    <div class="rounded-t mb-0 px-4 py-3 border-0">
                        <div class="flex flex-wrap items-center">
                            <div
                                    class="relative w-full  sm:flex-row sm:justify-between sm:items-center gap-5 file:px-4 max-w-full flex-col flex "
                                >
                                <div class="mb-3 sm:flex-row flex-col flex gap-3">

                                    <div class="flex flex-row gap-2">
                                    <button class="border px-4 py-1.5 border-gray-200 hover:bg-orange-400 hover:text-white rounded-md font-light bg-white">
                                        Csv
                                    </button>

                                    </div>
                                </div>
                                    <form class="flex items-center">

                                        <label
                                            for="simple-search"
                                            class="sr-only"
                                            >Search</label
                                        >
                                        <div class="relative w-full">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                            >
                                                <svg
                                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                                    aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 18 20"
                                                >
                                                    <path
                                                        stroke="currentColor"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5v10M3 5a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm0 10a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm0 0V6a3 3 0 0 0-3-3H9m1.5-2-2 2 2 2"
                                                    />
                                                </svg>
                                            </div>
                                            <input
                                                type="text"
                                                id="simple-search"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search in table..."
                                                v-model="searchQuery"
                                            />
                                        </div>
                                    </form>
                                </div>
                        </div>
                    </div>
                    <div class="block w-full overflow-x-auto">
                        <table
                            class="items-center w-full bg-transparent border-collapse"
                        >
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left bg-blueGray-50 text-blueGray-500 border-blueGray-100"
                                        v-for="column in columns"
                                        :key="column.field"
                                    >
                                        {{ column.label }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, rowIndex) in slicedRows"
                                    :key="rowIndex"
                                >
                                    <td
                                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                                        v-for="(value, colIndex) in columns"
                                        :key="colIndex"
                                    >
                                        <div>
                                            <div
                                                v-if="
                                                    value.field ===
                                                    'contact_number'
                                                "
                                            >
                                                {{ row.user.phone_number }}
                                            </div>
                                            <div v-if="value.field == 'status'" class="mt-2">
                                                <button class="bg-orange-500 p-1 mx-1 text-white rounded-md text-xs"
                                                    v-if="!row.is_approved"
                                                    :disabled="true"
                                                >
                                                    PENDING
                                                </button>

                                                <button class="bg-cyan-900 p-1 mx-1 text-white rounded-md text-xs"
                                                    v-if="!!row.is_approved"
                                                    :disabled="true"
                                                >
                                                    APPROVED
                                                </button>
                                            </div>

                                            <div
                                                v-if="value.field === 'action'"
                                            >
                                                <button
                                                    class="bg-orange-500 p-3 mx-1 text-white rounded-md text-xs"
                                                    :disabled="!!row.is_approved"
                                                    :class="{'cursor-not-allowed' : !!row.is_approved}"
                                                    @click="openTermsModal(row)"
                                                >
                                                    View
                                                </button>
                                            </div>
                                            <div v-else>
                                                {{ row[value.field] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div
                            class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800"
                        >
                        <div class="block w-full overflow-x-auto">
                                <div class="justify-between items-center block md:flex">
                                    <div class="flex items-center justify-start flex-wrap mb-3">
                                    <button
                                        @click="changePage(-1)"
                                        :disabled="currentPage == 1"
                                        :class="{
                                            'hidden': currentPage == 1,
                                        }"
                                        type="button"
                                        class="text-gray-500 bg-white mr-5 hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Previous
                                    </button>
                                    <button
                                        @click="changePage(1)"
                                        :disabled="currentPage === totalPages"
                                        type="button"
                                        class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10"

                                    >
                                        Next
                                    </button>
                                    </div>
                                    <div class="flex items-center justify-center">
                                    <small>Page {{ currentPage }}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                    id="defaultModal"
                    tabindex="-1"
                    aria-hidden="true"
                    style="background-color: rgba(0, 0, 0, 0.7)"
                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full"
                >
                    <div class="h-screen flex justify-center items-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow">
                                <!-- Modal header -->
                                <div
                                    class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600"
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Validation ID
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                        @click="closeTermsModal()"
                                    >
                                        <svg
                                            class="w-3 h-3"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 14 14"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                                            />
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <div class="p-6 space-y-6">
                                    <div class="w-full mt-5" v-if="id_picture">
                                        <label> ID Picture</label> <br/>
                                        <img :src="id_picture" alt="id_picture"
                                            style="width: 100%; height: 250px;"
                                        >
                                    </div>

                                    <div class="w-full mt-5" v-if="selfie_id_picture">
                                        <label> Selfie ID Picture</label> <br/>
                                        <img :src="selfie_id_picture" alt="selfie_id_picture"
                                            style="width: 100%; height: 250px;"
                                        >
                                    </div>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600"
                                    v-if="user"
                                >
                                    <button
                                        v-if="!user.is_approved"
                                        @click="changeStatus(user.id, false)"
                                        type="button"
                                        class="text-white bg-red-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Decline
                                    </button>
                                    <button
                                        v-if="!user.is_approved"
                                        @click="changeStatus(user.id, true)"
                                        type="button"
                                        class="text-white bg-green-500 hover:bg-gray-100 hover:text-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Approve
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </AuthenticatedLayout>
</template>

