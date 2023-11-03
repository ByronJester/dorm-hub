<template>

<section
  class="relative mx-5 xl:mx-20 mt-20 mb-5"
>
<div class="absolute rounded-2xl bg-cover h-[400px] bg-center bg-no-repeat inset-0 bg-[url(https://images.unsplash.com/photo-1604014237800-1c9102c219da?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80)]"></div>
<div class="absolute inset-0 h-[400px] rounded-2xl bg-black bg-opacity-50"></div>
  <div
    class="relative mx-auto max-w-screen-xl px-4 py-32 sm:px-6 lg:flex h-[400px]  lg:items-center lg:px-8"
  >
    <div class="max-w-xl text-center ltr:sm:text-left rtl:sm:text-right  ">
      <h1 class="text-3xl font-extrabold sm:text-5xl text-white">
        Your all in one

        <strong class="block font-extrabold text-orange-400">
          Dormitory Solution
        </strong>
      </h1>

      <p class="mt-4 max-w-lg text-white sm:text-xl/relaxed">
        Find the Perfect Dorm, Manage with Ease!
      </p>

      <div class="mt-8 flex flex-col p-5 shadow  rounded-md bg-white gap-4 text-center">

        <form>   
    <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
        </div>
        <input type="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Location..." required>
        <button type="submit" class="text-white absolute right-2.5 bottom-2.5 bg-orange-400 hover:bg-orange-600 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-4 py-2">Search</button>
    </div>
</form>
      </div>
    </div>
  </div>
</section>
</template>
