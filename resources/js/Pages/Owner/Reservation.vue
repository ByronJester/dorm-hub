
<script>
    import AuthenticatedLayout from '@/Layouts/SidebarLayout.vue';
    // import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';
    import { ref, computed, reactive, watch, onMounted, defineProps } from 'vue';
    import { usePage, useForm } from '@inertiajs/vue3'
    import axios from 'axios';
    import AppDropdown from "@/Pages/Owner/Components/AppDropDown.vue";
    import AppDropdownContent from "@/Pages/Owner/Components/AppDropDownContent.vue";
    import AppDropdownItem from "@/Pages/Owner/Components/AppDropDownItem.vue";
    import DataTable from 'primevue/datatable';
    import Button from 'primevue/button';
    import Tag from 'primevue/tag';
    import Column from 'primevue/column';
    import ColumnGroup from 'primevue/columngroup';   // optional
    import Row from 'primevue/row';      
    import { FilterMatchMode, FilterOperator } from 'primevue/api';

    export default {
        components: {
            DataTable,
            Button,
            Tag,
            Column,
            Row,
            ColumnGroup,
            AuthenticatedLayout,
            AppDropdown,
            AppDropdownContent,
            AppDropdownItem,
        },
        setup() {
            const isMobileView = ref(false)
            isMobileView.value = screen.width < 600;
            const filters = ref();
            const page = usePage()

            const user = page.props.auth.user;


            const getDaysRemaining = (reservation_date, expired_date) => {
                const date1 = new Date(reservation_date);
                const date2 = new Date(expired_date);

                // To calculate the time difference
                const timeDiff = Math.abs(date2.getTime() - date1.getTime());

                // Convert time difference to days
                return Math.ceil(timeDiff / (1000 * 3600 * 24));
            }

            const getStatus = (isActive) => {
                const active = isActive == true;
                const expired = isActive == false;
                let status = '';

                // To calculate the time difference

                // Assuming you have some logic to determine if it's active or expired
                if (active) {
                    status = 'Active';
                } else if (expired) {
                    status = 'Expired';
                }

                return status;
            }

            const getSeverity = (status) => {
                switch (status) {
                    case 'Expired':
                        return 'danger';

                    case 'Active':
                        return 'success';

                }
            };
            var dataReserve = [];
            
            const rows = ref(dataReserve);
            

            const reservations = page.props.reservations;

            for (let x = 0; x < reservations.length; x++) {
                dataReserve.push(
                    {
                        dorm_name: reservations[x].dorm.property_name,
                        room_name: reservations[x].room.name,
                        tenant_name: reservations[x].tenant_user.name,
                        visit_date: reservations[x].check_date,
                        time_visit: reservations[x].check_time,
                        remaining: getDaysRemaining(reservations[x].created_at, reservations[x].expired_at),
                        status: getStatus(reservations[x].is_active),
                        created_at: reservations[x].created_at, 
                    }
                );
            }
            dataReserve.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            const initFilters = () => {
                filters.value = {
                    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
                    dorm_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    room_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    tenant_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                    time_visit: { value: null, matchMode: FilterMatchMode.IN },
                    remaining: { value: null, matchMode: FilterMatchMode.IN },
                    visit_date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
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

            return {
                rows,
                clearFilter,
                initFilters,
                formatDate,
                filters,
                dataReserve,
                isMobileView,
                user,
                reservations,
                getSeverity
            }
        }
    }

</script>

<template>
    <AuthenticatedLayout>
        <!---->
        <div class="px-4 pt-20 lg:ml-64">
            <div class="flex items-center gap-3 justify-start">
                <i class="fa-regular fa-calendar fa-xl" style="color: #000000;"></i>
                <h3 class="text-3xl">Reservation</h3>
            </div>
            <hr class="h-px my-5 bg-orange-400 border-1 dark:bg-gray-700" />
            <div class="card mb-20">
                <DataTable v-model:filters="filters" :value="rows" tableStyle="min-width: 50rem" :rowsPerPageOptions="[5, 10, 20, 50]" class="border" paginator :rows="10"
                :globalFilterFields="['dorm_name', 'room_name', 'tenant_name', 'time_visit', 'visit_date', 'remaining', 'status']">
                <template #header>
                    <div class="flex items-center justify-between">
                        <Button type="button" class="rounded-lg border-green-400 border px-3 py-2.5" icon="fa-solid fa-filter-circle-xmark" label="Clear" outlined @click="clearFilter()" />
                        <span class="p-input-icon-left">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input v-model="filters['global'].value" placeholder="Keyword Search" class="pl-10 rounded-lg" />
                        </span>
                    </div>
                </template>
                <template #empty> No reservation found. </template>
                    <Column field="dorm_name" header="Dorm Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.dorm_name }}
                        </template>
                    </Column>
                    <Column field="room_name" header="Room Name" sortable dataType="date" style="min-width: 10rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.room_name }}                        
                        </template>
                    </Column>
                    <Column field="tenant_name" header="Tenant Name" sortable style="min-width: 14rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.tenant_name }}
                        </template>
                    </Column>
                    <Column header="Visit Date" field="visit_date" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ formatDate(data.visit_date) }}
                        </template>
                    </Column>
                    <Column header="Visit Time" field="time_visit" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.time_visit }}
                        </template>
                    </Column>
                    <Column header="Remaining Days" field="remaining" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            {{ data.remaining }}
                        </template>
                    </Column>
                    <Column header="Status" field="status" sortable style="min-width: 12rem" class="border-b">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="getSeverity(data.status)" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


