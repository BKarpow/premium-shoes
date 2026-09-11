<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { FreeMode, Navigation, Thumbs } from 'swiper/modules'
import { Fancybox } from '@fancyapps/ui'

// Стилі Swiper та Fancybox
import 'swiper/css'
import 'swiper/css/free-mode'
import 'swiper/css/navigation'
import 'swiper/css/thumbs'
import '@fancyapps/ui/dist/fancybox/fancybox.css'

const props = defineProps({
  images: {
    type: Array,
    default: () => []
  },
  productTitle: {
    type: String,
    default: ''
  }
})

// Зберігаємо екземпляр слайдера для зв'язки мініатюр з головним фото
const thumbsSwiper = ref(null)

const setThumbsSwiper = (swiper) => {
  thumbsSwiper.value = swiper
}

// Ініціалізація Fancybox (модальне вікно, зум, стрілки, Esc)
onMounted(() => {
  Fancybox.bind('[data-fancybox="gallery"]', {
    Hash: false,
    Thumbs: {
      autoStart: true,
    },
    Toolbar: {
      display: {
        left: ["infobar"],
        middle: [
          "zoomIn",
          "zoomOut",
          "toggle1to1",
          "rotateCCW",
          "rotateCW",
          "flipX",
          "flipY",
        ],
        right: ["slideshow", "thumbs", "close"],
      },
    },
  })
})

// Очищення при розмонтуванні компонента
onUnmounted(() => {
  Fancybox.destroy()
})
</script>

<template>
  <div class="w-full flex flex-col-reverse md:flex-row gap-4 items-start select-none min-w-0">

    <!-- МІНІАТЮРИ (Swiper Thumbs) -->
    <div v-if="images && images.length > 1" class="w-full md:w-24 flex-shrink-0">
      <Swiper
        @swiper="setThumbsSwiper"
        :direction="'horizontal'"
        :breakpoints="{
          768: { direction: 'vertical' }
        }"
        :spaceBetween="10"
        :slidesPerView="4"
        :freeMode="true"
        :watchSlidesProgress="true"
        :modules="[FreeMode, Navigation, Thumbs]"
        class="thumbs-swiper max-h-[480px] w-full"
      >
        <SwiperSlide
          v-for="(img, idx) in images"
          :key="img.id || idx"
          class="cursor-pointer rounded-xl overflow-hidden border-2 border-neutral-800 transition-all opacity-60 hover:opacity-100"
        >
          <img :src="img.path" :alt="productTitle" class="w-full h-20 object-cover rounded-lg" />
        </SwiperSlide>
      </Swiper>
    </div>

    <!-- ГОЛОВНЕ ЗОБРАЖЕННЯ (Основний Swiper + Fancybox) -->
    <div class="flex-1 w-full min-w-0 bg-neutral-900/60 border border-neutral-800 rounded-2xl overflow-hidden relative group">
      <Swiper
        :spaceBetween="10"
        :thumbs="{ swiper: thumbsSwiper }"
        :modules="[FreeMode, Navigation, Thumbs]"
        class="main-swiper h-[380px] sm:h-[480px] w-full"
      >
        <SwiperSlide
          v-for="(img, idx) in images"
          :key="img.id || idx"
          class="flex items-center justify-center p-4"
        >
          <!-- Клік по посиланню відкриває Fancybox -->
          <a
            :href="img.path"
            data-fancybox="gallery"
            :data-caption="`${productTitle} - Фото ${idx + 1}`"
            class="w-full h-full flex items-center justify-center cursor-zoom-in"
          >
            <img
              :src="img.path"
              :alt="productTitle"
              class="max-w-full max-h-full object-contain pointer-events-none group-hover:scale-105 transition-transform duration-300"
            />
          </a>
        </SwiperSlide>
      </Swiper>

      <!-- Підказка при наведенні -->
      <div class="absolute bottom-3 right-3 bg-neutral-950/80 backdrop-blur-md text-neutral-300 text-[10px] uppercase font-bold tracking-wider px-3 py-1.5 rounded-lg border border-neutral-800 pointer-events-none z-10 flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
        </svg>
        <span>Відкрити повний розмір</span>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* Стилізація активної мініатюри */
.thumbs-swiper :deep(.swiper-slide-thumb-active) {
  border-color: #f59e0b !important; /* amber-500 */
  opacity: 1 !important;
  box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2);
}
</style>
