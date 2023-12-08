<script >
import Rooms from '@/Components/RoomCard.vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { usePage } from '@inertiajs/vue3';

export default{
    components:{
        Rooms,
        SidebarLayout
    },
    setup(){
        const page = usePage()
        const room = page.props.dorm;
        const user = page.props.user;
        return{
            room,
            user
        }
    }
}

</script>
<template>
    <SidebarLayout>
        <div class="p-4 mt-20  lg:ml-64">
            <p class="text-4xl mb-5 font-bold">Rooms</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 mx-3 gap-2">
                <Rooms :dorms.sync="room" :user.sync="user"/>
            </div>
        </div>
    </SidebarLayout>
    
</template>