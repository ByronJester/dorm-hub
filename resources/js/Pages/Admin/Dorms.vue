<script>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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

            const openModal = (image) => {
                business_permit.value = image

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
                openModal,
                closeModal,
                changeStatus
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <div class="main">
            <div class="w-full pt-2 md:pt-20">
                <vue-good-table
                    styleClass="vgt-table condensed"
                    style="padding: 0vw 5vw; width: 100%"
                    :columns="columns"
                    :rows="rows"
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
                                @click="openModal(props.row.business_permit_image)"
                            >
                                View
                            </button>

                            <button class="bg-rose-500 p-3 mx-1 text-white rounded-md text-xs"
                                v-if="props.row.status == 'pending'"
                                @click="changeStatus('declined', props.row.id)"
                            >
                                Decline
                            </button>

                            <button class="bg-cyan-900 p-3 mx-1 text-white rounded-md text-xs"
                                v-if="props.row.status == 'pending'"
                                @click="changeStatus('approved', props.row.id)"
                            >
                                Approve
                            </button>
                        </div>
                    </template>
                </vue-good-table>
            </div>

            <div class="w-full">
                <div id="bpModal" class="bpModal mt-10 md:mt-0">
                    <div class="bp-modal-content flex flex-col" :style="{width: isMobileView ? '97%' : '30%'}">
                        <div class="w-full">
                            <span class="text-lg font-bold">
                                Business Permit
                            </span>
                            <span class="float-right cursor-pointer"
                                @click="closeModal()"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </span>
                        </div>

                        <div class="w-full mt-5">
                            <img :src="business_permit" alt="business_permit"
                                style="width: 100%; height: 500px;"
                            >
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style>
    .main {
        height: 100%;
        min-height: 92vh;
        background-color: #E5E8E8;
    }

    .bpModal {
        display: none;
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
    .bp-modal-content {
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

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* make scrollbar transparent */
    }

</style>
