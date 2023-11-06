<script>
export default{
  props:{
    heroImage: String,
    title: String,
    subtitle: String,
    description: String,
    query: String
  }
}

</script>

<template>

<section
  class="relative mx-5 xl:mx-20 mt-20 mb-5"
>
<img 
            alt="bg-image"
            class="absolute rounded-2xl object-cover w-full h-[400px] bg-center shadow-lg bg-no-repeat inset-0 "
            :src="heroImage ?? 'https://images.unsplash.com/photo-1604014237800-1c9102c219da?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'"
            />
        
<div class="absolute inset-0 h-[400px] shadow-lg rounded-2xl bg-black bg-opacity-50"></div>
  <div
    class="relative mx-auto max-w-screen-xl px-4 py-32 sm:px-6 lg:flex h-[400px]  lg:items-center lg:px-8"
  >
    <div class="max-w-xl text-center ltr:sm:text-left rtl:sm:text-right  ">
      <h1 class="text-3xl font-extrabold sm:text-5xl text-white">
     {{ title }}

        <strong class="block font-extrabold text-orange-400">
           {{ subtitle }}
        </strong>
      </h1>

      <p class="mt-4 max-w-lg text-white sm:text-xl/relaxed">
        {{ description }}
      </p>

    </div>
  </div>
</section>
</template>
