<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";
import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
import { ref, onMounted, computed } from "vue";
import { usePage, useForm, router} from "@inertiajs/vue3";
import axios from "axios";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import DataTable from "primevue/datatable";
import Button from "primevue/button";
import Tag from "primevue/tag";
import Column from "primevue/column";
import ColumnGroup from "primevue/columngroup"; // optional
import Row from "primevue/row";
import Dropdown from 'primevue/dropdown'
import { FilterMatchMode, FilterOperator } from "primevue/api";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
export default {
    components: {
        AuthenticatedLayout,
        AppDropdown,
        AppDropdownContent,
        AppDropdownItem,
        InputLabel,
        TextInput,
        InputError,
        DataTable,
        Button,
        Tag,
        Column,
        Dropdown,
        Row,
        ConfirmDialog,
        Toast
    },
    setup() {
        const page = usePage();
        const filters = ref();
        const confirm = useConfirm();
        const toast = useToast();
        const headerTenants = [
            "Room Name",
            "Tenant Name",
            "Room Price",
            "Move-In Date",
            "Move-Out Date",
            "Balance",
        ];
        const statuses = ref(['Active','Delinquent']);

        const getSeverity = (status) => {
                switch (status) {
                    case 'Delinquent':
                        return 'danger';

                    case 'Active':
                        return 'success';


                }
            };

            const initFilters = () => {
                filters.value = {
                    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                    room_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    tenant_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    fee: { value: null, matchMode: FilterMatchMode.IN },
                    move_in: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                    status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                };
            };

            initFilters();

            const formatDate = (value) => {
                // Check if value is a string and convert it to a Date object
                const date = typeof value === 'string' ? new Date(value) : value;

                // Check if date is a valid Date object
                if (isNaN(date.getTime())) {
                    // If not a valid Date, you can handle it according to your requirements
                    return "Invalid Date"; // or return value.toString() or any other representation
                }

                // If it's a valid Date object, format it
                return date.toLocaleDateString('en-US', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            };

            const clearFilter = () => {
                initFilters();
            };


            const options = page.props.dorms.filter(dorm => {
                return dorm && dorm.status === 'approved';
            });

        const selectedDorm = ref(options[0].id);

        const tenantsData = ref([]);

        const tenants = page.props.tenants;
        const balance = page.props.feeBalances;
        const constructData = (dorm_id) => {
            var arrTenant = [];

            for (let t = 0; t < tenants.length; t++) {
                arrTenant.push({
                    id: tenants[t].id,
                    dorm_id: tenants[t].dorm_id,
                    room_name: tenants[t].room.name,
                    tenant_name: tenants[t].profile.first_name,
                    fee: tenants[t].room.fee,
                    move_in: tenants[t].move_in,
                    move_out: tenants[t].move_out,
                    status:tenants[t].status,
                    balance:balance[t].balance,
                    checkfee: tenants[t].room.fee * 2,  
                    checkbalance:balance[t].balance * 2,
                    delinquent:tenants[t].is_delinquent,
                    profileId:tenants[t].profile_id
                });
            }

            return arrTenant.filter((x) => {
                return x.dorm_id == dorm_id;
            });
        };

        const objectRemoveKey = (object, key = null) => {
            const newObject = Object.assign({}, object);

            delete newObject.id;
            delete newObject.dorm_id;

            return newObject;
        };

        onMounted(() => {
            tenantsData.value = constructData(selectedDorm.value);
            console.log(tenantsData.value)
        });

        const moneyFormat = (amount) => {
            amount = parseFloat(amount).toFixed(2);

            return (
                "₱ " + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        };

        const dormChange = (evt) => {
            let dorm_id = evt.target.value;

            tenantsData.value = constructData(dorm_id);
        };

        const openComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "block";
        };

        const changeTenantStatus = (id) => {
            confirm.require({
                message: 'Are you sure you want to mark this tenant as delinquent?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios.post(route('change.tenant.status', id))
                        .then(response => {
                            toast.add({ severity: 'info', summary: 'Success', detail: 'Successfully mark this tenant as delinquent ', life: 3000 });
                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch(error => {

                        });
                },
                reject: () =>{

                }
            });

        }

        const changeTenantStatusActive = (id) => {
            confirm.require({
                message: 'Are you sure you want to mark this tenant as active?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios.post(route('change.tenant.statusactive', id))
                        .then(response => {
                            toast.add({ severity: 'info', summary: 'Success', detail: 'Successfully change status as active', life: 3000 });
                            setTimeout(function () {
                                location.reload()
                            }, 3000);
                        })
                        .catch(error => {

                        });
                },
                reject: () =>{

                }
            });
        }

        const noticeTermination = (arg) => {
            confirm.require({
                message: 'Are you sure you want to notice this tenant for termination?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(
                            route("tenant.notice.termination", { id: arg.id }),
                            {
                                id: arg.refund_id,
                            }
                        )
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully add termination notice', life: 3000 });

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {});
                },
                reject: () =>{

                }
            });
        };

        const removeTenant = (arg) => {
            confirm.require({
                message: 'Are you sure you want to remove this tenant?',
                header: 'Confirmation',
                icon: 'fa-solid fa-triangle-exclamation',
                accept: () => {
                    axios
                        .post(route("tenant.remove", { id: arg.id }), {
                            id: arg.refund_id,
                        })
                        .then((response) => {
                            toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Successfully remove this tenant', life: 3000 });

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {});
                },
                reject: () =>{

                }
            });
        };

        const closeComplainModal = () => {
            var modal = document.getElementById("complainModal");

            modal.style.display = "none";
        };

        const form = ref({
            first_name: null,
            last_name: null,
            username: null,
            phone_number: null,
            password: null,
            confirm_password: null,
            id_picture: null,
            selfie_id_picture: null,
            image: null,
            source_of_income: null,
            monthly_income: null,
            proof: null,
            room: null
        })

        const idPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                form.value.id_picture = e.target.result;
            };
        };

        const selfieIdPictureChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                form.value.selfie_id_picture = e.target.result;
            };
        };

        const proofImageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                form.value.proof = e.target.result;
            };
        };

        const imageChange = (e) => {
            const image = e.target.files[0];

            const reader = new FileReader();

            reader.readAsDataURL(image);

            reader.onload = (e) => {
                form.value.image = e.target.result;
            };
        };

        const imageClick = () => {
            document.getElementById("profile_image").click();
        };

        const createTenant = () => {
            swal(
                {
                    title: `Are you sure to add this tenant?`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false,
                },
                function () {
                    axios
                        .post(route("tenant.add"), form.value)
                        .then((response) => {
                            swal(
                                "Success!",
                                `You successfully add this tenant.`,
                                "success"
                            );

                            setTimeout(function () {
                                location.reload();
                            }, 3000);
                        })
                        .catch((error) => {});
                }
            );
        }

        console.log(page.props)
        const view = (id) => {
            var url = null;
            router.get(route('owner.tenantshistory', id));
            
        };
        return {
            options,
            view,
            headerTenants,
            selectedDorm,
            tenantsData,
            objectRemoveKey,
            tenants,
            moneyFormat,
            dormChange,
            noticeTermination,
            removeTenant,
            openComplainModal,
            closeComplainModal,
            form,
            idPictureChange,
            selfieIdPictureChange,
            imageChange,
            proofImageChange,
            imageClick,
            createTenant,
            filters,
            clearFilter,
            getSeverity,
            formatDate,
            statuses,
            changeTenantStatus,
            changeTenantStatusActive
        };
    },
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="px-4 pt-20 lg:ml-64">
            <div class="w-full">
                <div class="flex items-center justify-start">
                    <span
                        class="inline-flex justify-center items-center w-12 h-12 rounded-full bg-white text-black mr-3"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            height="24px"
                            viewBox="0 0 640 512"
                        >
                            <path
                                d="M335.5 4l288 160c15.4 8.6 21 28.1 12.4 43.5s-28.1 21-43.5 12.4L320 68.6 47.5 220c-15.4 8.6-34.9 3-43.5-12.4s-3-34.9 12.4-43.5L304.5 4c9.7-5.4 21.4-5.4 31.1 0zM320 160a40 40 0 1 1 0 80 40 40 0 1 1 0-80zM144 256a40 40 0 1 1 0 80 40 40 0 1 1 0-80zm312 40a40 40 0 1 1 80 0 40 40 0 1 1 -80 0zM226.9 491.4L200 441.5V480c0 17.7-14.3 32-32 32H120c-17.7 0-32-14.3-32-32V441.5L61.1 491.4c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l37.9-70.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c16.3 0 31.9 4.5 45.4 12.6l33.6-62.3c15.3-28.5 45.1-46.3 77.5-46.3h19.5c32.4 0 62.1 17.8 77.5 46.3l33.6 62.3c13.5-8.1 29.1-12.6 45.4-12.6h19.5c32.4 0 62.1 17.8 77.5 46.3l37.9 70.3c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8L552 441.5V480c0 17.7-14.3 32-32 32H472c-17.7 0-32-14.3-32-32V441.5l-26.9 49.9c-6.3 11.7-20.8 16-32.5 9.8s-16-20.8-9.8-32.5l36.3-67.5c-1.7-1.7-3.2-3.6-4.3-5.8L376 345.5V400c0 17.7-14.3 32-32 32H296c-17.7 0-32-14.3-32-32V345.5l-26.9 49.9c-1.2 2.2-2.6 4.1-4.3 5.8l36.3 67.5c6.3 11.7 1.9 26.2-9.8 32.5s-26.2 1.9-32.5-9.8z"
                            />
                        </svg>
                    </span>
                    <h3 class="text-3xl">Manage Tenants</h3>
                </div>

                <hr class="h-px my-5 bg-orange-400 border-1 " />
                <div>
                    <p class="text-sm font-bold">Dorm:</p>
                    <select
                        v-model="selectedDorm"
                        id="subject"
                        @change="dormChange($event)"
                        class="block w-56 px-4 py-1.5 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500  "
                    >
                        <option
                            v-for="option in options"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.property_name }}
                        </option>
                    </select>
                </div>
                <div class="card mt-5">
                <DataTable v-model:filters="filters" :value="tenantsData" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10" filterDisplay="menu"
                :globalFilterFields="['room_name', 'tenant_name', 'fee', 'move_in', 'status']">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div>
                            <Button type="button" class="rounded-lg border-orange-400 border px-3 py-2.5" icon="pi pi-user-plus" label="Add Tenant" outlined @click="openComplainModal()" />
                            <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5 ml-5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                        </div>

                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                        </span>
                    </div>
                </template>
                <template #empty> No tenants found. </template>
                    <Column field="room_name" header="Room Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.room_name }}
                        </template>
                    </Column>
                    <Column field="tenant_name" header="Tenant Name" sortable dataType="date" style="min-width: 10rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.tenant_name }}
                        </template>
                    </Column>
                    <Column header="Moved-In Date" field="move_in" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.move_in }}
                        </template>
                    </Column>
                    <Column field="fee" header="Monthly Fee" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ moneyFormat(data.fee) }}
                        </template>
                    </Column>
                    <Column field="balance" header="Monthly Fee Balance" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ moneyFormat(data.balance) }}
                        </template>
                    </Column>
                    <Column field="status" header="Status" sortable style="min-width:  14rem" :filterMenuStyle="{ width: '14rem' }" class="border-b">
                        <template #body="{ data }">
                            <Tag v-if="!data.is_delinquent" value="Active" :severity="getSeverity('Active')" />
                            <Tag v-if="!!data.is_delinquent" value="Delinquent" :severity="getSeverity('Delinquent')" />
                        </template>
                        <template #filter="{ filterModel }">
                            <Dropdown v-model="filterModel.value" :options="statuses" placeholder="Select One" class="p-column-filter" showClear>
                                <template #option="slotProps">
                                    <Tag :value="slotProps.option" :severity="getSeverity(slotProps.option)" />
                                </template>
                            </Dropdown>
                        </template>
                    </Column>

                    <!--Column header="Action" style="min-width: 5rem" class="border-b">
                        <template #body ="{data}">
                            <AppDropdown class="flex justify-center items-center">
                                    <ConfirmDialog />
                                    <Toast />
                                    <button
                                    class="hover:text-orange-400"
                                    >
                                        <i  class="pi pi-ellipsis-h"></i>
                                    </button>
                                                <AppDropdownContent class="bg-white z-50 ">
                                                    <div>
                                                        <AppDropdownItem v-if="!data.is_delinquent && data.balance == data.checkfee" @click="changeTenantStatus(data.profileId)">
                                                            Mark as Delinquent
                                                        </AppDropdownItem>
                                                        <AppDropdownItem v-if="data.is_delinquent" @click="changeTenantStatusActive(data.profileId)">
                                                            Mark as Active
                                                        </AppDropdownItem>
                                                    </div>

                                                    <AppDropdownItem @click="noticeTermination(data)">
                                                        Notice Termination
                                                    </AppDropdownItem>
                                                    <AppDropdownItem @click="removeTenant(data);">
                                                        Remove Tenant 
                                                    </AppDropdownItem>

                                                    <AppDropdownItem @click="view(data.profileId)" >
                                                        Remove Tenant 
                                                    </AppDropdownItem>
                                                </AppDropdownContent>
                                    </AppDropdown>
                        </template>
                    </Column-->
                </DataTable>
            </div>

                <div
                    id="complainModal"
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
                                    class="flex items-start justify-between p-4 border-b rounded-t "
                                >
                                    <h3
                                        class="text-xl font-semibold text-black"
                                    >
                                        Add Tenant
                                    </h3>
                                    <button
                                        type="button"
                                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center "
                                        @click="closeComplainModal()"
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
                                    <form>
                                        <div
                                            class="flex flex-col gap-3 items-center justify-center"
                                        >
                                            <div class="flex mb-3 md:mb-0">
                                                <div>
                                                    <img
                                                        :src="!!form.image ? form.image : 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                                                        alt="Profile picture"
                                                        class="rounded-full block md:h-40 w-40 bg-no-repeat bg-cover object-fit max-w-full bg-gray-100 "
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <div class="inline-flex" @click="imageClick()">
                                                    <a
                                                        class="inline-flex justify-center items-center whitespace-nowrap focus:outline-none transition-colors focus:ring duration-150 border cursor-pointer rounded border-blue-600 ring-blue-300 py-2 px-3"
                                                        disabled="false"
                                                    >
                                                        <span
                                                            class="inline-flex justify-center items-center w-6 h-6"
                                                            ><svg
                                                                viewBox="0 0 24 24"
                                                                width="16"
                                                                height="16"
                                                                class="inline-block"
                                                            >
                                                                <path
                                                                    fill="currentColor"
                                                                    d="M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z"
                                                                ></path>
                                                            </svg> </span
                                                        ><span class="px-2"
                                                            >Upload</span
                                                        >

                                                        <input
                                                            @change="
                                                                imageChange(
                                                                    $event
                                                                )
                                                            "
                                                            accept="image/png, image/gif, image/jpeg"
                                                            id="profile_image"
                                                            type="file"
                                                            class="absolute top-0 left-0 opacity-0 outline-none -z-1"
                                                        />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-5" />
                                        <div
                                            class="grid grid-cols-2 gap-2 mt-2"
                                        >
                                            <div>
                                                <InputLabel
                                                    for="first_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="First Name"
                                                />

                                                <TextInput
                                                    id="first_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Juan"
                                                    required
                                                    autofocus
                                                    autocomplete="first_name"
                                                    v-model="form.first_name"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="last_name"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Last Name"
                                                />

                                                <TextInput
                                                    id="middle_name"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Casimero"
                                                    autofocus
                                                    autocomplete="middle_name"
                                                    v-model="form.last_name"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="username"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Username"
                                                />

                                                <TextInput
                                                    id="username"
                                                    type="text"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: Jear12"
                                                    autofocus
                                                    autocomplete="middle_name"
                                                    v-model="form.username"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="phone_number"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Contact"
                                                />

                                                <input
                                                    id="phone_number"
                                                    type="number"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="ex: 09112233445"
                                                    required
                                                    autocomplete="phone_number"
                                                    v-model="form.phone_number"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="password"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Password"
                                                />

                                                <TextInput
                                                    id="password"
                                                    type="password"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="*********"
                                                    autofocus
                                                    v-model="form.password"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div>
                                                <InputLabel
                                                    for="confirm_password"
                                                    class="block mb-2 text-sm font-medium text-gray-900 "
                                                    value="Confirm Password"
                                                />

                                                <TextInput
                                                    id="confirm_password"
                                                    type="password"
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5 "
                                                    placeholder="*********"
                                                    autofocus
                                                    v-model="form.confirm_password"
                                                />

                                                <InputError class="mt-2" />
                                            </div>

                                            <div class="mt-4">
                                                <div>
                                                    <div
                                                        class="flex gap-2 items-center"
                                                    >
                                                        <InputLabel
                                                            for="id_picture"
                                                            class="block text-sm font-medium text-gray-900 "
                                                            value="Valid ID"
                                                        />
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            fill="none"
                                                            color="orange"
                                                            viewBox="0 0 24 24"
                                                            stroke-width="1.5"
                                                            stroke="currentColor"
                                                            class="w-5 h-5 cursor-pointer hover:bg-orange-400 animate-bounce"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"
                                                            />
                                                        </svg>
                                                    </div>
                                                    <span
                                                        class="text-xs text-red-500"
                                                        >(ex: Passport, National
                                                        Id, etc For Student:
                                                        School ID)</span
                                                    >
                                                </div>

                                                <input
                                                    type="file"
                                                    id="id_picture"
                                                    class="hidden"
                                                    accept="image/*"
                                                    @change="idPictureChange($event)"
                                                />

                                                <label
                                                    for="id_picture"
                                                    class="relative cursor-pointer"
                                                >
                                                    <div
                                                        class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                    >
                                                        <img
                                                        :src="!!form.id_picture ? form.id_picture : 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                                                            alt="Valid ID"
                                                            class="h-48 w-auto rounded-lg"
                                                        />
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" />
                                            </div>
                                            <div class="mt-4">
                                                <div>
                                                    <div
                                                        class="flex gap-2 items-center"
                                                    >
                                                        <InputLabel
                                                            for="selfie_id_picture"
                                                            class="block text-sm font-medium text-gray-900 "
                                                            value="Selfie withValid ID"
                                                        />
                                                    </div>
                                                    <span
                                                        class="text-xs text-red-500"
                                                        >Add selfie holding the
                                                        valid id</span
                                                    >
                                                </div>

                                                <input
                                                    type="file"
                                                    id="selfie_id_picture"
                                                    class="hidden"
                                                    accept="image/*"
                                                    @change="selfieIdPictureChange($event)"
                                                />

                                                <label
                                                    for="selfie_id_picture"
                                                    class="relative cursor-pointer"
                                                >
                                                    <div
                                                        class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                    >
                                                        <img
                                                        :src="!!form.selfie_id_picture ? form.selfie_id_picture : 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                                                            alt="Valid ID"
                                                            class="h-48 w-auto rounded-lg"
                                                        />
                                                    </div>
                                                </label>

                                                <InputError class="mt-2" />
                                            </div>

                                            <div class="mt-4">
                                                <div class="w-full">
                                                    <InputLabel
                                                        for="source_of_income"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Source of Income"
                                                    />

                                                    <select
                                                        class="w-full mt-1 rounded-md"
                                                        v-model="form.source_of_income"
                                                    >
                                                        <option value="Salary">
                                                            Salary
                                                        </option>
                                                        <option
                                                            value="Business"
                                                        >
                                                            Business
                                                        </option>
                                                        <option value="Pension">
                                                            Pension
                                                        </option>
                                                        <option
                                                            value="Allowance"
                                                        >
                                                            Allowance
                                                        </option>
                                                    </select>

                                                    <InputError class="mt-2" />
                                                </div>

                                                <div class="w-full mt-3">
                                                    <InputLabel
                                                        for="monthly_income"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Monthly Income"
                                                    />

                                                    <TextInput
                                                        id="monthly_income"
                                                        type="number"
                                                        class="mt-1 block w-full"
                                                        required
                                                        v-model="form.monthly_income"
                                                    />

                                                    <InputError class="mt-2" />
                                                </div>
                                            </div>

                                            <div
                                                class="w-full flex flex-col mt-4"
                                            >
                                                <div class="w-full">
                                                    <InputLabel
                                                        for="proof"
                                                        class="block text-sm font-medium text-gray-900 "
                                                        value="Proof of Income"
                                                    />

                                                    <input
                                                        type="file"
                                                        id="proof"
                                                        class="hidden"
                                                        accept="image/*"
                                                        @change="proofImageChange($event)"
                                                    />

                                                    <label
                                                        for="proof"
                                                        class="relative cursor-pointer"
                                                    >
                                                        <div
                                                            class="h-48 bg-gray-200 border border-dashed border-gray-400 flex justify-center items-center rounded-lg"
                                                        >
                                                            <img
                                                                :src="!!form.proof ? form.proof : 'https://api.dicebear.com/7.x/avataaars/svg?seed=doe-doe-doe-example-com'"
                                                                alt="Proof"
                                                                class="h-48 w-auto rounded-lg"
                                                            />
                                                        </div>
                                                    </label>

                                                    <InputError class="mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-5" />
                                        <div class="w-full">
                                            <InputLabel
                                                for="assign_room"
                                                class="block text-sm font-medium text-gray-900 "
                                                value="Assign Room"
                                            />

                                            <select
                                                class="w-full mt-1 rounded-md"
                                                v-model="form.room"
                                            >
                                                <option
                                                    v-for="room in options.filter(x =>  {
                                                        return x.id == selectedDorm
                                                    })[0].rooms"

                                                    :key="room.id"
                                                    :value="room.id"
                                                >
                                                    {{ room.name }}
                                                </option>
                                            </select>

                                            <InputError class="mt-2" />
                                        </div>
                                    </form>
                                </div>
                                <!-- Modal footer -->
                                <div
                                    class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b "
                                >
                                    <button
                                        @click.prevent="createTenant()"
                                        type="button"
                                        class="text-white bg-orange-400 hover:bg-orange-200 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
