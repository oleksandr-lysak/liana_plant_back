<template>
  <div class="max-w-5xl mx-auto px-4 py-8 space-y-10">
    <!-- SEO -->
    <Head>
      <title>{{ master.data.name }} – Персональна сторінка майстра</title>
      <meta name="description" :content="master.description" />
    </Head>

    <!-- Профіль -->
    <section class="flex flex-col sm:flex-row items-center gap-6">
      <img
        :src="`/${master.data.main_photo}`"
        alt="Фото майстра"
        class="w-36 h-36 sm:w-44 sm:h-44 rounded-2xl object-cover border-4 dark:border-green-500 border-blue-500 shadow-md transition-transform hover:scale-105 duration-300"
      />
      <div class="flex-1 space-y-2 text-center sm:text-left">
        <h1 class="text-3xl font-bold dark:text-white text-gray-900">{{ master.data.name }}</h1>
        <p class="text-gray-600 dark:text-gray-300">{{ master.data.description }}</p>
        <div class="flex flex-wrap justify-center sm:justify-start items-center gap-3 text-gray-700 dark:text-gray-300 text-sm">
          <span><i class="fa fa-location-dot mr-1"></i>{{ master.data.address }}</span>
          <span class="opacity-50">|</span>
          <span><i class="fa fa-phone mr-1"></i>{{ master.data.phone }}</span>
          <span class="opacity-50">|</span>
          <span>Вік: {{ master.data.age }}</span>
        </div>
      </div>
    </section>

    <!-- Рейтинг -->
    <section class="flex items-center gap-2">
      <i class="fa fa-star text-yellow-400 text-xl"></i>
      <span class="text-lg font-medium text-gray-800 dark:text-gray-300">Рейтинг: {{ master.data.rating }}/5</span>
    </section>

    <!-- Спеціалізації -->
    <section>
      <h2 class="text-2xl font-semibold mb-3 dark:text-white text-gray-800">Спеціалізації</h2>
      <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
        <li v-for="service in master.data.services" :key="service">{{ service.name }}</li>
      </ul>
    </section>

    <!-- Відгуки -->
    <section>
      <h2 class="text-2xl font-semibold mb-3 dark:text-white text-gray-800">Відгуки</h2>
      <div v-if="reviews.length" class="space-y-4">
        <div
          v-for="review in reviews"
          :key="review.id"
          class="bg-white dark:bg-gray-700 p-5 rounded-2xl shadow-md"
        >
          <div class="flex justify-between items-center">
            <span class="font-semibold text-gray-900 dark:text-white">{{ review.user_name }}</span>
            <span class="text-yellow-500">
              <i class="fa fa-star"></i> {{ review.rating }}
            </span>
          </div>
          <p class="mt-2 text-gray-700 dark:text-gray-300 text-sm">{{ review.comment }}</p>
        </div>
      </div>
      <p v-else class="text-sm text-gray-500 dark:text-gray-400">Відгуків поки немає.</p>
    </section>

    <!-- Форма відгуку -->
    <section>
      <h2 class="text-2xl font-semibold mb-3 dark:text-white text-gray-800">Залишити відгук</h2>
      <form @submit.prevent="submitReview" class="grid gap-4 md:grid-cols-2">
        <input v-model="newReview.user" required placeholder="Ваше ім’я" class="input md:col-span-2" />
        <textarea v-model="newReview.comment" required placeholder="Ваш відгук" class="input h-24 md:col-span-2" />
        <select v-model="newReview.rating" class="input">
          <option disabled value="">Оцініть майстра</option>
          <option v-for="i in 5" :key="i" :value="i">{{ i }}</option>
        </select>
        <button type="submit" class="btn-primary w-full md:w-auto md:justify-self-end">
          Надіслати
        </button>
      </form>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  master: Object
});

const reviews = ref([]);
const newReview = ref({ user: '', comment: '', rating: '' });



onMounted(() => {
  console.log('Master name:', props.master.data);
});

function submitReview() {
  reviews.value.push({ ...newReview.value, id: Date.now() });
  newReview.value = { user: '', comment: '', rating: '' };
}

</script>

<style scoped>
.input {
  @apply w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-white transition duration-200;
}
.btn-primary {
  @apply bg-blue-500 text-white px-5 py-2 rounded-xl hover:bg-blue-600 transition duration-200;
}
.btn-success {
  @apply bg-green-500 text-white px-5 py-2 rounded-xl hover:bg-green-600 transition duration-200;
}
</style>
