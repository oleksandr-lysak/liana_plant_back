<template>
  <div
    class="flex flex-col sm:flex-row justify-between sm:items-center dark:bg-gray-800 bg-white shadow-md mt-2 p-4 hover:shadow-xl rounded-2xl cursor-pointer transition-all duration-300">
    
    <div class="flex flex-col sm:flex-row items-start">
      <img
        :src="imageUrl || 'https://i.imgur.com/aq39RMA.jpeg'"
        class="w-24 h-24 sm:w-28 sm:h-28 rounded-lg object-cover border-2 dark:border-green-400 border-blue-500 shadow-sm"
      />
      <div class="flex flex-col ml-0 sm:ml-4 mt-2 sm:mt-0 space-y-1">
        <span class="font-semibold text-lg dark:text-gray-100 text-gray-800">{{ name }}</span>
        <span class="text-sm dark:text-gray-300 text-gray-500 truncate max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg">
          {{ description }}
        </span>
        <div class="flex items-center flex-wrap gap-x-2 text-sm dark:text-green-400 text-blue-500">
          <span>Age: {{ age }}</span>
          <span>|</span>
          <span>Experience: 5 years</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2 text-sm dark:text-gray-400 text-gray-700">
          <div class="flex items-center space-x-1">
            <i class="fa fa-solid fa-location-dot"></i>
            <span>{{ address }}</span>
          </div>
          <span class="hidden sm:inline">|</span>
          <div class="flex items-center space-x-1 mt-1 sm:mt-0">
            <i class="fa fa-solid fa-phone"></i>
            <span>{{ phone }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col sm:items-end items-start mt-4 sm:mt-0 space-y-1">
      <span class="text-sm dark:text-gray-300 text-gray-500">2 reviews</span>
      <div class="flex items-center space-x-1">
        <i class="fa fa-star dark:text-green-400 text-blue-500"></i>
        <span class="text-sm font-medium dark:text-gray-400 text-gray-600">{{ rating }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps({
  masterId: Number,
  imageUrl: String,
  description: String,
  address: String,
  name: String,
  phone: String,
  age: Number,
  rating: Number,
});
</script>
