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
                    label: 'Dorm Owner',
                    field: 'dorm_owner',
                },
                {
                    label: 'Contact Number',
                    field: 'contact_number',
                },
                {
                    label: 'Property Name',
                    field: 'property_name',
                },
                {
                    label: 'Dorm Address',
                    field: 'detailed_address',
                },
                {
                    label: 'Floor Total',
                    field: 'floors_total',
                },

                {
                    label: 'Room Total',
                    field: 'rooms_total',
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
                rows.value = page.props.dorms
            });

            const business_permit = ref(null)
            const dorm = ref(null)

            const openModal = (arg) => {
                business_permit.value = arg.business_permit_image
                dorm.value = arg

                var modal = document.getElementById("bpModal");

                modal.style.display = "block";

            }

            const closeModal = () => {
                var modal = document.getElementById("bpModal");

                modal.style.display = "none";
            }

            const changeStatus = (status, id) => {
                var s = status == 'declined' ? 'decline' : 'approved';

                swal({
                    title: `Are you sure to ${s} this dorm?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function(){
                    axios.post(route('dorm.change.status', status), {id: id})
                        .then(response => {
                            swal("Success!", `You successfully ${status} this dorm.`, "success");

                            setTimeout(function () {
                                location.reload()
                            }, 1500);
                        })
                        .catch(error => {
                            errors.value = error.response.data.errors
                        })
                });


            }


            return {
                isMobileView,
                columns,
                rows,
                business_permit,
                
                dorm,
                openModal,
                closeModal,
                changeStatus
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 lg:ml-64">
            <div class="flex items-center justify-start mt-16">
                <span class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black dark:bg-slate-900/70 dark:text-white mr-3">
                    <svg viewBox="0 0 24 24" width="24" height="24" class="inline-block"><path fill="currentColor" d="M3,14L3.5,14.07L8.07,9.5C7.89,8.85 8.06,8.11 8.59,7.59C9.37,6.8 10.63,6.8 11.41,7.59C11.94,8.11 12.11,8.85 11.93,9.5L14.5,12.07L15,12C15.18,12 15.35,12 15.5,12.07L19.07,8.5C19,8.35 19,8.18 19,8A2,2 0 0,1 21,6A2,2 0 0,1 23,8A2,2 0 0,1 21,10C20.82,10 20.65,10 20.5,9.93L16.93,13.5C17,13.65 17,13.82 17,14A2,2 0 0,1 15,16A2,2 0 0,1 13,14L13.07,13.5L10.5,10.93C10.18,11 9.82,11 9.5,10.93L4.93,15.5L5,16A2,2 0 0,1 3,18A2,2 0 0,1 1,16A2,2 0 0,1 3,14Z"></path></svg>
                </span>
                <h3 class="text-3xl">Dorm Verification</h3>
            </div>
            <div
                class="grid grid-cols-2 lg:grid-cols-3 sm:grid-cols-2 gap-4 mb-4 mt-4 text-gray-400 dark:text-white"
            >
                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                         {{pends}}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Declined Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            <!--
                            {{
                                props.rows.status =='pending'
                            }}-->
                        </p>
                        <p class="text-xs">TOTAL NO. OF Approved Dorm</p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center h-32 rounded-lg shadow-lg bg-gray-50 dark:bg-gray-800"
                >
                    <div class="text-center p-4">
                        <p class="text-2xl mb-2">
                            {{ appr }}
                        </p>
                        <p class="text-xs">TOTAL NO. OF Pending Dorm</p>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <vue-good-table
                    styleClass="vgt-table bordered"
                    style="width: 100%"
                    :line-numbers="{ enabled: true }"
                    :columns="columns"
                    :rows="rows"
                    theme="black-rhino"
                    
                    :pagination-options="{ enabled: true }"
                    :select-options="{ enabled: false }"
                    :search-options="{ enabled: true }"
                >
                    <template #table-row="props">
                        <div v-if="props.column.field == 'dorm_owner'">
                            {{ props.row.user.first_name }} {{ props.row.user.last_name }}
                        </div>

                        <div v-if="props.column.field == 'contact_number'">
                            {{ props.row.user.phone_number }}
                        </div>

                        <div v-if="props.column.field == 'status'" class="mt-2">
                            <button class="bg-orange-500 p-1 mx-1 text-white rounded-sm text-xs"
                                v-if="props.row.status == 'pending'"
                                :disabled="true"
                            >
                                Pending
                            </button>

                            <button class="bg-rose-500 p-1 mx-1 text-white rounded-md text-xs"
                                v-if="props.row.status == 'declined'"
                                :disabled="true"
                            >
                                DECLINED
                            </button>

                            <button class="bg-cyan-900 p-1 mx-1 text-white rounded-md text-xs"
                                v-if="props.row.status == 'approved'"
                                :disabled="true"
                            >
                                APPROVED
                            </button>
                        </div>

                        <div v-if="props.column.field == 'action'">
                            <button class="bg-orange-500 p-3 mx-1 text-white rounded-md text-xs"
                                @click="openModal(props.row)"
                            >
                                View
                            </button>
                        </div>
                    </template>
                </vue-good-table>
            </div>

            <div class="w-full">
                <div id="bpModal" class="bpModal hidden mt-20 md:mt-0">
                    <div class="bp-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">

                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5" v-if="business_permit">
                            <img :src="business_permit" alt="business_permit"
                                style="width: 100%; height: 250px;"
                            >
                        </div>

                        <div class="w-full mt-5" v-if="dorm && dorm.status == 'pending'">
                            <img :src="dorm.user.id_picture" alt="business_permit"
                                style="width: 100%; height: 250px;"
                            >
                        </div>

                        <div class="w-full mt-5" v-if="dorm">
                            <button class="bg-cyan-900 p-3 mx-1 text-white rounded-md text-xs float-right"
                                v-if="dorm.status == 'pending'"
                                @click="changeStatus('approved', dorm.id)"
                            >
                                Approve
                            </button>

                            <button class="bg-rose-500 p-3 mx-1 text-white rounded-md text-xs float-right"
                                v-if="dorm.status == 'pending'"
                                @click="changeStatus('declined', dorm.id)"
                            >
                                Decline
                            </button>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style>


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
