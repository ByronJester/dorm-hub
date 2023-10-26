<script>
import AuthenticatedLayout from "@/Layouts/SidebarLayout.vue";

export default{
    components:{
        AuthenticatedLayout
    },
    setup() {
        const goBack = () => {
            this.$router.go(-1);
        };

        return {
        goBack,
        };
    },
   
}
</script>
<template>
  <div>
    <a @click="goBack">
        back
    </a>
  </div>

            income report
        

</template>