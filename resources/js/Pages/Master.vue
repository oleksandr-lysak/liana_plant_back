<template>
  <div class="master-page">
    <div class="master-card">
      <div class="master-header">
        <img :src="master.photo" :alt="`Фото майстра ${master.name}`" class="master-photo" />
        <h1 class="master-name">{{ master.name }}</h1>
      </div>
      <p class="master-description" itemprop="description">{{ master.description }}</p>
      <section class="services-section">
        <h2>Послуги</h2>
        <ul class="services-list">
          <li v-for="service in master.services" :key="service.id" itemprop="service">
            <span class="service-name">{{ service.name }}</span> - <span class="service-price">{{ service.price }}$</span>
          </li>
        </ul>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: "MasterPage",
  data() {
    return {
      master: {
        id: 1,
        name: "Іван Петренко",
        photo: "/images/john_doe.jpg",
        description: "Досвідчений перукар з понад 10 роками досвіду в індустрії.",
        services: [
          { id: 1, name: "Стрижка", price: 30 },
          { id: 2, name: "Фарбування волосся", price: 50 },
          { id: 3, name: "Корекція бороди", price: 20 },
        ],
      },
    };
  },
  head() {
    return {
      title: this.master.name + " - Послуги перукаря",
      meta: [
        {
          hid: "description",
          name: "description",
          content: this.master.description,
        },
        {
          hid: "og:title",
          property: "og:title",
          content: this.master.name + " - Послуги перукаря",
        },
        {
          hid: "og:description",
          property: "og:description",
          content: this.master.description,
        },
        {
          hid: "og:image",
          property: "og:image",
          content: this.master.photo,
        },
      ],
    };
  },
};
</script>

<style scoped>
.master-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f4f4f4;
}

.master-card {
  background-color: white;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 30px;
  max-width: 600px;
  width: 90%;
  margin: 20px;
}

.master-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 20px;
}

.master-photo {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
}

.master-name {
  font-size: 28px;
  font-weight: 600;
  margin: 10px 0;
  color: #333;
}

.master-description {
  margin: 20px 0;
  font-size: 18px;
  line-height: 1.6;
  color: #555;
}

.services-section {
  margin-top: 30px;
}

.services-list {
  list-style: none;
  padding: 0;
}

.services-list li {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
  font-size: 18px;
  padding: 10px;
  border-bottom: 1px solid #eee;
  color: #333;
}

.service-name {
  font-weight: 500;
}

.service-price {
  color: #007bff;
}

/* Адаптивність для мобільних пристроїв */
@media (max-width: 600px) {
  .master-card {
    padding: 20px;
  }

  .master-photo {
    width: 120px;
    height: 120px;
  }

  .master-name {
    font-size: 24px;
  }

  .master-description {
    font-size: 16px;
  }

  .services-list li {
    font-size: 16px;
  }
}
</style>