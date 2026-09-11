<script setup>
import { ref, watch } from 'vue';
import {Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array,
    brands: Array,
    sizes: Array,
});

const form = useForm({
    title: '',
    slug: '',
    category_id: '',
    brand_id: '',
    price: '',
    old_price: '',
    description: '',
    is_active: true,
    images: [],    // Масив файлів
    variants: [],  // Масив об'єктів [{ size_id: 1, stock: 5, sku: '' }]
});

const imagePreviews = ref([]);

// 1. Генерація slug (з підтримкою кирилиці)
watch(() => form.title, (newTitle) => {
    form.slug = newTitle
        .toLowerCase()
        .trim()
        .replace(/ /g, '-')
        .replace(/[^\w\u0400-\u04FF-]/g, '')
        .replace(/--+/g, '-');
});

// 2. Метод завантаження фотографій
const handleMultipleImages = (event) => {
    const selectedFiles = Array.from(event.target.files);
    if (!selectedFiles.length) return;

    // Додаємо файли у форму
    form.images = [...form.images, ...selectedFiles];

    // Створюємо прев'ю
    selectedFiles.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value.push({
                file: file,
                url: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });

    event.target.value = '';
};

// 3. Видалення фото з попереднього перегляду
const removeImage = (index) => {
    form.images.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

// 4. Методи для роботи з розмірами (Варіантами)
const isSizeSelected = (sizeId) => {
    return form.variants.some(v => v.size_id === sizeId);
};

const toggleSize = (sizeId) => {
    const index = form.variants.findIndex(v => v.size_id === sizeId);
    if (index > -1) {
        // Якщо вже обрано — видаляємо
        form.variants.splice(index, 1);
    } else {
        // Якщо не обрано — додаємо об'єкт варіанта
        form.variants.push({
            size_id: sizeId,
            stock: 1,
            sku: ''
        });
    }
};

const getStockForSize = (sizeId) => {
    const variant = form.variants.find(v => v.size_id === sizeId);
    return variant ? variant.stock : 1;
};

const updateStock = (sizeId, newStock) => {
    const variant = form.variants.find(v => v.size_id === sizeId);
    if (variant) {
        variant.stock = Math.max(0, parseInt(newStock) || 0);
    }
};

// 5. Відправка форми з multipart/form-data
const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
};
</script>

<template>
<!-- Встановлюємо заголовок вкладки браузера -->
    <Head title="Створення нового товару" />
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6 md:p-8 font-sans">
        <div class="max-w-5xl mx-auto">
            <!-- Шапка -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-800">
                <div>
                    <h1 class="text-2xl font-bold text-white">Додати новий товар</h1>
                    <p class="text-xs text-slate-400 mt-1">Заповніть інформацію, завантажте фото та оберіть розміри</p>
                </div>
                <Link :href="route('admin.products.index')" class="text-xs font-semibold text-slate-400 hover:text-white transition">
                    ← Назад до списку
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">

                <!-- 1. Основна інформація -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
                    <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">1. Основна інформація</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Назва товару *</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Кросівки Nike Air Max"
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none"
                                required
                            />
                            <span v-if="form.errors.title" class="text-xs text-rose-500 mt-1 block">{{ form.errors.title }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Slug (URL) *</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none"
                                required
                            />
                            <span v-if="form.errors.slug" class="text-xs text-rose-500 mt-1 block">{{ form.errors.slug }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Категорія *</label>
                            <select v-model="form.category_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none" required>
                                <option value="" disabled>Оберіть категорію</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <span v-if="form.errors.category_id" class="text-xs text-rose-500 mt-1 block">{{ form.errors.category_id }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Бренд *</label>
                            <select v-model="form.brand_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none" required>
                                <option value="" disabled>Оберіть бренд</option>
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                            <span v-if="form.errors.brand_id" class="text-xs text-rose-500 mt-1 block">{{ form.errors.brand_id }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Ціна (грн) *</label>
                            <input v-model="form.price" type="number" step="0.01" placeholder="3500" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none" required />
                            <span v-if="form.errors.price" class="text-xs text-rose-500 mt-1 block">{{ form.errors.price }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Стара ціна (грн, акція)</label>
                            <input v-model="form.old_price" type="number" step="0.01" placeholder="4200" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Опис товару</label>
                        <textarea v-model="form.description" rows="4" placeholder="Опис матеріалу, підошви..." class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4 bg-slate-950 border-slate-800 rounded text-amber-500 focus:ring-0" />
                        <label for="is_active" class="text-sm font-medium text-slate-300">Опублікувати товар одразу</label>
                    </div>
                </div>

                <!-- 2. Фотографії -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">2. Галерея фотографій *</h2>
                        <span class="text-xs text-slate-500">Завантажено: {{ imagePreviews.length }} фото</span>
                    </div>

                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-800 border-dashed rounded-lg cursor-pointer bg-slate-950 hover:bg-slate-900/50 hover:border-amber-500/50 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <span class="text-2xl mb-1">📸</span>
                                <p class="mb-1 text-xs text-slate-300"><span class="font-semibold text-amber-400">Натисніть для вибору файлів</span></p>
                                <p class="text-[10px] text-slate-500">Можна вибирати одразу кілька фото (JPG, PNG, WEBP)</p>
                            </div>
                            <input type="file" multiple accept="image/*" @change="handleMultipleImages" class="hidden" />
                        </label>
                    </div>
                    <span v-if="form.errors.images" class="text-xs text-rose-500 block">{{ form.errors.images }}</span>

                    <!-- Прев'ю фотографій -->
                    <div v-if="imagePreviews.length > 0" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 pt-3">
                        <div v-for="(img, idx) in imagePreviews" :key="idx" class="relative group aspect-square rounded-lg overflow-hidden border border-slate-800 bg-slate-950">
                            <img :src="img.url" class="w-full h-full object-cover" />
                            <span v-if="idx === 0" class="absolute top-1.5 left-1.5 bg-amber-500 text-slate-950 font-bold text-[9px] px-1.5 py-0.5 rounded shadow">
                                Головне
                            </span>
                            <button type="button" @click="removeImage(idx)" class="absolute top-1.5 right-1.5 bg-rose-600 hover:bg-rose-500 text-white w-6 h-6 rounded flex items-center justify-center text-xs shadow">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. Розміри та залишки -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                    <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">3. Наявні розміри та залишки *</h2>
                    <p class="text-xs text-slate-400">Клікніть на розмір, щоб додати його до товару:</p>

                    <!-- Кнопки розмірів -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <button
                            v-for="s in sizes"
                            :key="s.id"
                            type="button"
                            @click="toggleSize(s.id)"
                            :class="isSizeSelected(s.id) ? 'bg-amber-500 text-slate-950 border-amber-400 font-bold shadow-md shadow-amber-500/20' : 'bg-slate-950 text-slate-400 border-slate-800 hover:border-slate-700'"
                            class="px-4 py-2 rounded-lg border text-xs transition"
                        >
                            {{ s.value }} EU
                        </button>
                    </div>

                    <!-- Таблиця доданих розмірів для вводу кількості -->
                    <div v-if="form.variants.length > 0" class="mt-6 border-t border-slate-800 pt-4">
                        <h3 class="text-xs font-semibold text-slate-300 uppercase mb-3">Налаштування залишків:</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div v-for="s in sizes.filter(size => isSizeSelected(size.id))" :key="s.id" class="p-3 bg-slate-950 border border-slate-800 rounded-lg flex items-center justify-between gap-3">
                                <span class="text-sm font-bold text-amber-400">{{ s.value }} EU</span>
                                <div class="flex items-center gap-2">
                                    <label class="text-[11px] text-slate-400">Кількість:</label>
                                    <input
                                        :value="getStockForSize(s.id)"
                                        @input="e => updateStock(s.id, e.target.value)"
                                        type="number"
                                        min="0"
                                        class="w-20 bg-slate-900 border border-slate-800 rounded px-2 py-1 text-xs text-white text-center focus:border-amber-500 focus:outline-none"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <span v-if="form.errors.variants" class="text-xs text-rose-500 block">{{ form.errors.variants }}</span>
                </div>

                <!-- Кнопка збереження -->
                <div class="flex justify-end gap-4 pt-4">
                    <Link :href="route('admin.products.index')" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-lg text-sm transition">
                        Скасувати
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg text-sm transition shadow-lg shadow-amber-500/10 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Збереження...' : 'Зберегти товар' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
