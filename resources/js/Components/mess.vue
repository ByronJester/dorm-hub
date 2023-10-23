
<TenantLayout v-if="user.user_type == 'tenant'">
    <main class="flex-grow flex mt-16 flex-row min-h-0">
              <!--Sidebar-->
              <section class="flex flex-col flex-none overflow-auto w-24 group lg:max-w-sm md:w-2/5 transition-all duration-300 ease-in-out">
                  <div class="header p-4 flex flex-row justify-between items-center flex-none">
                      <div class="w-16 h-16 relative flex flex-shrink-0" style="filter: invert(100%);">
                          <img class="rounded-full w-full h-full object-cover" alt="ravisankarchinnam" src="https://avatars3.githubusercontent.com/u/22351907?s=60">
                      </div>
                      <p class="text-md font-bold hidden md:block group-hover:block">Messenger</p>
                      <a href="#" class=" rounded-full hover:bg-gray-700 bg-gray-800 w-10 h-10 p-2 hidden md:block group-hover:block">
                          <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                              <path d="M6.3 12.3l10-10a1 1 0 0 1 1.4 0l4 4a1 1 0 0 1 0 1.4l-10 10a1 1 0 0 1-.7.3H7a1 1 0 0 1-1-1v-4a1 1 0 0 1 .3-.7zM8 16h2.59l9-9L17 4.41l-9 9V16zm10-2a1 1 0 0 1 2 0v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2h6a1 1 0 0 1 0 2H4v14h14v-6z"></path>
                          </svg>
                      </a>
                  </div>
                  <div class="search-box p-4 flex-none">
                      <form onsubmit="">
                          <div class="relative">
                              <label>
                                  <input class="rounded-full py-2 pr-6 pl-10 w-full border border-gray-800 focus:border-gray-700 bg-gray-800 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in" type="text" value="" placeholder="Search Messenger">
                                  <span class="absolute top-0 left-0 mt-2 ml-3 inline-block">
                                      <svg viewBox="0 0 24 24" class="w-6 h-6">
                                          <path fill="#bbb" d="M16.32 14.9l5.39 5.4a1 1 0 0 1-1.42 1.4l-5.38-5.38a8 8 0 1 1 1.41-1.41zM10 16a6 6 0 1 0 0-12 6 6 0 0 0 0 12z"></path>
                                      </svg>
                                  </span>
                              </label>
                          </div>
                      </form>
                  </div>
  
                  <div class="contacts p-2 flex-1 overflow-y-scroll">
                      <div class="flex justify-between items-center p-3 hover:bg-gray-800 rounded-lg relative"
                              >
                        <!--Big Image-->
                          <div class="w-16 h-16 relative flex flex-shrink-0">
                              <img class="shadow-md rounded-full w-full h-full object-cover" src="https://randomuser.me/api/portraits/men/32.jpg" alt="User2">
                          </div>
                          <!--Name-->
                          <div class="flex-auto min-w-0 ml-4 mr-6 hidden md:block group-hover:block">
                              <p>
                                <!--{{
                                      user.user_type  == 'owner' ? thread.tenant.name : thread.owner.name
                                  }}-->
                                YOW
                                  </p>
                              <div class="flex items-center text-sm text-gray-600">
                                  <div class="min-w-0">
                                    <!--NameSender-->
                                      <p class="truncate">Happy birthday to you my friend!
                                      </p>
                                  </div>
                                  <p class="ml-2 whitespace-no-wrap">2 Oct</p>
                              </div>
                          </div>
                          <div class="w-4 h-4 flex flex-shrink-0 hidden md:block group-hover:block">
                            <!--Small Image-->
                              <img class="rounded-full w-full h-full object-cover" alt="user2" src="https://randomuser.me/api/portraits/men/32.jpg">
                          </div>
                      </div>
                  </div>
              </section>
              <!--endSidebar-->
              <section class="flex flex-col flex-auto border-l border-gray-800">
                <!--ChatHeader-->
                  <div class="chat-header px-6 py-4 flex flex-row flex-none justify-between items-center shadow">
                      <div class="flex">
                          <div class="w-12 h-12 mr-4 relative flex flex-shrink-0">
                              <img class="shadow-md rounded-full w-full h-full object-cover" src="https://randomuser.me/api/portraits/women/33.jpg" alt="">
                          </div>
                          <div class="text-sm">
                              <p class="font-bold">
                                Scarlet
                                <!--{{
                                      user.user_type  == 'owner' ? selectedThread.tenant.name : selectedThread.owner.name
                                  }}
                              </p>-->
                              </p>
                              <p>Active 1h ago</p>
                          </div>
                      </div>
                  </div>
                <!--endChatHeader-->
                  <div class="chat-body p-4 flex-1 overflow-y-scroll">
                    <!--SenderMessage-->
                      <div class="flex flex-row justify-start">
                          <!--SenderChatImage-->
                          <div class="w-8 h-8 relative flex flex-shrink-0 mr-4">
                              <img class="shadow-md rounded-full w-full h-full object-cover" src="https://randomuser.me/api/portraits/women/33.jpg" alt="">
                          </div>
                          <!--SenderChatImage-->
                          <div class="messages text-sm text-gray-700 grid grid-flow-row gap-2">
                            <!--Message-->
                              <div class="flex items-center group">
                                  <p class="px-6 py-3 rounded-t-full rounded-r-full bg-gray-800 max-w-xs lg:max-w-md text-gray-200">Hey! How are you?</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
   M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
  C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                              <div class="flex items-center group">
                                  <p class="px-6 py-3 rounded-r-full bg-gray-800 max-w-xs lg:max-w-md text-gray-200">Shall we go for Hiking this weekend?</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
   M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
  C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                              <div class="flex items-center group">
                                  <p class="px-6 py-3 rounded-b-full rounded-r-full bg-gray-800 max-w-xs lg:max-w-md text-gray-200">Lorem ipsum
                                      dolor sit
                                      amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                                      dolore magna aliqua. Volutpat lacus laoreet non curabitur gravida.</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
   M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
  C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                            <!--Message-->
                          </div>
                          
                      </div>
                    <!--SenderMessage-->
                      <!--Chattime-->
                      <p class="p-4 text-center text-sm text-gray-500">12:40 PM</p>
                      <!--endChattime-->
                      <div class="flex flex-row justify-end">
                          <!--Receiver Message-->
                          <div class="messages text-sm text-white grid grid-flow-row gap-2">
                              <div class="flex items-center flex-row-reverse group">
                                  <p class="px-6 py-3 rounded-t-full rounded-l-full bg-blue-700 max-w-xs lg:max-w-md">Hey! How are you?</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
       M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
      C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                              <div class="flex items-center flex-row-reverse group">
                                  <p class="px-6 py-3 rounded-l-full bg-blue-700 max-w-xs lg:max-w-md">Shall we go for Hiking this weekend?</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
       M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
      C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                              <div class="flex items-center flex-row-reverse group">
                                  <a class="block w-64 h-64 relative flex flex-shrink-0 max-w-xs lg:max-w-md" href="#">
                                      <img class="absolute shadow-md w-full h-full rounded-l-lg object-cover" src="https://unsplash.com/photos/8--kuxbxuKU/download?force=true&amp;w=640" alt="hiking">
                                  </a>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
       M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
      C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                              <div class="flex items-center flex-row-reverse group">
                                  <p class="px-6 py-3 rounded-b-full rounded-l-full bg-blue-700 max-w-xs lg:max-w-md">Lorem ipsum
                                      dolor sit
                                      amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et
                                      dolore magna aliqua. Volutpat lacus laoreet non curabitur gravida.</p>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10.001,7.8C8.786,7.8,7.8,8.785,7.8,10s0.986,2.2,2.201,2.2S12.2,11.215,12.2,10S11.216,7.8,10.001,7.8z
       M3.001,7.8C1.786,7.8,0.8,8.785,0.8,10s0.986,2.2,2.201,2.2S5.2,11.214,5.2,10S4.216,7.8,3.001,7.8z M17.001,7.8
      C15.786,7.8,14.8,8.785,14.8,10s0.986,2.2,2.201,2.2S19.2,11.215,19.2,10S18.216,7.8,17.001,7.8z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M19,16.685c0,0-2.225-9.732-11-9.732V2.969L1,9.542l7,6.69v-4.357C12.763,11.874,16.516,12.296,19,16.685z"></path>
                                      </svg>
                                  </button>
                                  <button type="button" class="hidden group-hover:block flex flex-shrink-0 focus:outline-none mx-2 block rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-700 bg-gray-800 w-8 h-8 p-2">
                                      <svg viewBox="0 0 24 24" class="w-full h-full fill-current">
                                          <path d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-3.54-4.46a1 1 0 0 1 1.42-1.42 3 3 0 0 0 4.24 0 1 1 0 0 1 1.42 1.42 5 5 0 0 1-7.08 0zM9 11a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm6 0a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"></path>
                                      </svg>
                                  </button>
                              </div>
                          </div>
                          <!--Receiver Message-->
                      </div>
                  </div>
                  <!--Footer-->
                  <div class="chat-footer flex-none">
                      <div class="flex flex-row items-center p-4">
                          <!--Image-->
                          <button type="button" class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">
                              <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                  <path d="M11,13 L8,10 L2,16 L11,16 L18,16 L13,11 L11,13 Z M0,3.99406028 C0,2.8927712 0.898212381,2 1.99079514,2 L18.0092049,2 C19.1086907,2 20,2.89451376 20,3.99406028 L20,16.0059397 C20,17.1072288 19.1017876,18 18.0092049,18 L1.99079514,18 C0.891309342,18 0,17.1054862 0,16.0059397 L0,3.99406028 Z M15,9 C16.1045695,9 17,8.1045695 17,7 C17,5.8954305 16.1045695,5 15,5 C13.8954305,5 13,5.8954305 13,7 C13,8.1045695 13.8954305,9 15,9 Z"></path>
                              </svg>
                          </button>
                          <!--Image-->
                          <!--Camera-->
                          <button type="button" class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">
                              <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                  <path d="M0,6.00585866 C0,4.89805351 0.893899798,4 2.0048815,4 L5,4 L7,2 L13,2 L15,4 L17.9951185,4 C19.102384,4 20,4.89706013 20,6.00585866 L20,15.9941413 C20,17.1019465 19.1017876,18 18.0092049,18 L1.99079514,18 C0.891309342,18 0,17.1029399 0,15.9941413 L0,6.00585866 Z M10,16 C12.7614237,16 15,13.7614237 15,11 C15,8.23857625 12.7614237,6 10,6 C7.23857625,6 5,8.23857625 5,11 C5,13.7614237 7.23857625,16 10,16 Z M10,14 C11.6568542,14 13,12.6568542 13,11 C13,9.34314575 11.6568542,8 10,8 C8.34314575,8 7,9.34314575 7,11 C7,12.6568542 8.34314575,14 10,14 Z"></path>
                              </svg>
                          </button>
                          <!--Camera-->
                          <!--InputMessage-->
                          <div class="relative flex-grow">
                              <label>
                                  <input class="rounded-full py-2 pl-3 pr-10 w-full border border-gray-800 focus:border-gray-700 bg-gray-800 focus:bg-gray-900 focus:outline-none text-gray-200 focus:shadow-md transition duration-300 ease-in" type="text" value="" placeholder="Aa">
                                  <button type="button" class="absolute top-0 right-0 mt-2 mr-3 flex flex-shrink-0 focus:outline-none block text-blue-600 hover:text-blue-700 w-6 h-6">
                                      <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                          <path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM6.5 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm7 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm2.16 3a6 6 0 0 1-11.32 0h11.32z"></path>
                                      </svg>
                                  </button>
                              </label>
                          </div>
                          <!--InputMessage-->
                          <!--Send-->
                          <button type="button" class="flex flex-shrink-0 focus:outline-none mx-2 block text-blue-600 hover:text-blue-700 w-6 h-6">
                              <svg viewBox="0 0 20 20" class="w-full h-full fill-current">
                                  <path d="M11.0010436,0 C9.89589787,0 9.00000024,0.886706352 9.0000002,1.99810135 L9,8 L1.9973917,8 C0.894262725,8 0,8.88772964 0,10 L0,12 L2.29663334,18.1243554 C2.68509206,19.1602453 3.90195042,20 5.00853025,20 L12.9914698,20 C14.1007504,20 15,19.1125667 15,18.000385 L15,10 L12,3 L12,0 L11.0010436,0 L11.0010436,0 Z M17,10 L20,10 L20,20 L17,20 L17,10 L17,10 Z"></path>
                              </svg>
                          </button>
                          <!--Send-->
                      </div>
                  </div>
                  <!--Footer-->
              </section>
          </main>
    </TenantLayout>