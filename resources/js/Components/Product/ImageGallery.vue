<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

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

// Активне зображення
const currentIndex = ref(0)

// Стан Magnifier (зум при наведенні)
const isHovered = ref(false)
const lensPosition = ref({ x: 0, y: 0 })
const bgPosition = ref({ x: 0, y: 0 })
const mainImageRef = ref(null)

// Стан модального вікна
const isModalOpen = ref(false)
const isZoomed = ref(false)
const zoomOrigin = ref({ x: '50%', y: '50%' })

const currentImage = computed(() => {
  if (!props.images || props.images.length === 0) {
    return { path: 'https://via.placeholder.com/800' }
  }
  return props.images[currentIndex.value] || props.images[0]
})

const selectImage = (index) => {
  currentIndex.value = index
}

// Логіка Magnifier (працює тільки на десктопах від lg і вище)
const handleMouseMove = (e) => {
  if (!mainImageRef.value || window.innerWidth < 1024) return

  const rect = mainImageRef.value.getBoundingClientRect()
  let x = e.clientX - rect.left
  let y = e.clientY - rect.top

  const lensSize = 150
  const halfLens = lensSize / 2

  x = Math.max(halfLens, Math.min(x, rect.width - halfLens))
  y = Math.max(halfLens, Math.min(y, rect.height - halfLens))

  lensPosition.value = {
    x: x - halfLens,
    y: y - halfLens
  }

  const bgX = ((x - halfLens) / (rect.width - lensSize)) * 100
  const bgY = ((y - halfLens) / (rect.height - lensSize)) * 100

  bgPosition.value = {
    x: Math.max(0, Math.min(100, bgX)),
    y: Math.max(0, Math.min(100, bgY))
  }
}

const handleMouseEnter = () => {
  if (window.innerWidth >= 1024) {
    isHovered.value = true
  }
}

const handleMouseLeave = () => {
  isHovered.value = false
}

const openModal = () => {
  isHovered.value = false
  isModalOpen.value = true
  isZoomed.value = false
}

const closeModal = () => {
  isModalOpen.value = false
  isZoomed.value = false
}

const nextImage = () => {
  if (props.images.length === 0) return
  isZoomed.value = false
  currentIndex.value = (currentIndex.value + 1) % props.images.length
}

const prevImage = () => {
  if (props.images.length === 0) return
  isZoomed.value = false
  currentIndex.value = (currentIndex.value - 1 + props.images.length) % props.images.length
}

const toggleZoom = (event) => {
  if (isZoomed.value) {
    isZoomed.value = false
  } else {
    const rect = event.target.getBoundingClientRect()
    const x = ((event.clientX - rect.left) / rect.width) * 100
    const y = ((event.clientY - rect.top) / rect.height) * 100
    zoomOrigin.value = { x: `${x}%`, y: `${y}%` }
    isZoomed.value = true
  }
}

const handleKeydown = (e) => {
  if (!isModalOpen.value) return
  if (e.key === 'Escape') closeModal()
  if (e.key === 'ArrowRight') nextImage()
  if (e.key === 'ArrowLeft') prevImage()
}

onMounted(() => window.addEventListener('keydown', handleKeydown))
onUnmounted(() => window.removeEventListener('keydown', handleKeydown))
</script>

<template>
  <div class="relative w-full flex flex-col-reverse md:flex-row gap-4 items-start min-w-0">

    <!-- МІНІАТЮРИ -->
    <div
      v-if="images && images.length > 1"
      class="flex md:flex-col gap-2.5 overflow-x-auto md:overflow-y-auto max-h-[500px] custom-scrollbar flex-shrink-0 w-full md:w-auto"
    >
      <button
        v-for="(img, idx) in images"
        :key="img.id || idx"
        @mouseenter="selectImage(idx)"
        @click="selectImage(idx)"
        :class="[
          'relative w-16 h-16 md:w-20 md:h-20 rounded-xl overflow-hidden border-2 transition-all flex-shrink-0 bg-neutral-900 cursor-pointer',
          currentIndex === idx
            ? 'border-amber-500 opacity-100 shadow-lg shadow-amber-500/20 scale-95'
            : 'border-neutral-800 opacity-60 hover:opacity-100 hover:border-neutral-600'
        ]"
      >
        <img :src="img.path" :alt="productTitle" class="w-full h-full object-cover" />
      </button>
    </div>

    <!-- ГОЛОВНЕ ЗОБРАЖЕННЯ -->
    <div
      ref="mainImageRef"
      @mousemove="handleMouseMove"
      @mouseenter="handleMouseEnter"
      @mouseleave="handleMouseLeave"
      @click="openModal"
      class="relative w-full h-[380px] sm:h-[480px] md:h-[500px] bg-neutral-900/60 border border-neutral-800 rounded-2xl overflow-hidden cursor-pointer md:cursor-crosshair flex items-center justify-center p-4 select-none"
    >
      <img
        :src="currentImage.path"
        :alt="productTitle"
        class="max-w-full max-h-full w-auto h-auto object-contain pointer-events-none"
      />

      <!-- Лінза (тільки для десктопа) -->
      <div
        v-if="isHovered && window?.innerWidth >= 1024"
        :style="{
          left: `${lensPosition.x}px`,
          top: `${lensPosition.y}px`,
          width: '150px',
          height: '150px'
        }"
        class="absolute hidden lg:block border-2 border-amber-500/80 bg-amber-500/10 backdrop-brightness-125 rounded-lg pointer-events-none z-10 shadow-xl"
      />

      <!-- Бейдж -->
      <div class="absolute bottom-3 right-3 bg-neutral-950/80 backdrop-blur-md text-neutral-300 text-[10px] uppercase font-bold tracking-wider px-3 py-1.5 rounded-lg border border-neutral-800 pointer-events-none flex items-center gap-1.5 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
        </svg>
        <span>Натисніть для огляду</span>
      </div>
    </div>

    <!-- БЛОК ЗВІЛЬШЕНОГО ПЕРЕГЛЯДУ ЗБОКУ (Тільки для великих екранів lg:) -->
    <div
      v-if="isHovered && window?.innerWidth >= 1024"
      :style="{
        backgroundImage: `url('${currentImage.path}')`,
        backgroundPosition: `${bgPosition.x}% ${bgPosition.y}%`,
        backgroundSize: '250%'
      }"
      class="hidden lg:block absolute left-[calc(100%+1rem)] top-0 w-[450px] h-[500px] bg-no-repeat bg-neutral-950 border-2 border-amber-500/40 rounded-2xl shadow-2xl z-40 pointer-events-none"
    />

    <!-- МОДАЛЬНЕ ВІКНО (LIGHTBOX) -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="isModalOpen"
          class="fixed inset-0 z-50 bg-neutral-950/95 backdrop-blur-md flex items-center justify-center select-none overflow-hidden"
        >
          <button
            @click="closeModal"
            class="absolute top-5 right-5 z-20 text-neutral-400 hover:text-white bg-neutral-900/80 hover:bg-neutral-800 border border-neutral-800 p-2.5 rounded-full transition-colors cursor-pointer"
            title="Закрити (Esc)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <button
            v-if="images.length > 1"
            @click.stop="prevImage"
            class="absolute left-4 sm:left-8 z-20 text-neutral-300 hover:text-white bg-neutral-900/80 hover:bg-neutral-800 border border-neutral-800 p-3 rounded-full transition-colors cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <div
            @click="closeModal"
            class="w-full h-full p-4 sm:p-12 flex items-center justify-center overflow-hidden cursor-pointer"
          >
            <img
              :src="currentImage.path"
              :alt="productTitle"
              @click.stop="toggleZoom"
              :style="{
                transformOrigin: `${zoomOrigin.x} ${zoomOrigin.y}`,
                transform: isZoomed ? 'scale(2.2)' : 'scale(1)'
              }"
              :class="[
                'max-w-full max-h-full object-contain transition-transform duration-300 ease-out',
                isZoomed ? 'cursor-zoom-out' : 'cursor-zoom-in'
              ]"
            />
          </div>

          <button
            v-if="images.length > 1"
            @click.stop="nextImage"
            class="absolute right-4 sm:right-8 z-20 text-neutral-300 hover:text-white bg-neutral-900/80 hover:bg-neutral-800 border border-neutral-800 p-3 rounded-full transition-colors cursor-pointer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <div class="absolute bottom-5 z-20 flex items-center gap-4 bg-neutral-900/80 border border-neutral-800 px-4 py-2 rounded-xl text-xs text-neutral-400">
            <span>{{ currentIndex + 1 }} / {{ images.length }}</span>
            <span class="hidden sm:inline border-l border-neutral-800 pl-4">Клікніть по фото для збільшення</span>
          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
  height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #0a0a0a;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #262626;
  border-radius: 4px;
}
</style>
