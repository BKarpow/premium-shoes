<script setup>
import { ref } from 'vue';
import { useForm, Link, router, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    product: Object,
    categories: Array,
    brands: Array,
    sizes: Array,
});

const form = useForm({
    _method: 'PUT',
    title: props.product.title,
    slug: props.product.slug,
    category_id: props.product.category_id,
    brand_id: props.product.brand_id,
    price: props.product.price,
    old_price: props.product.old_price,
    description: props.product.description,
    is_active: Boolean(props.product.is_active),
    new_images: [],
    main_image_id: props.product.images.find(img => img.is_main)?.id || null,
    variants: props.product.variants.map(v => ({
        size_id: v.size_id,
        stock: v.stock,
        sku: v.sku
    })),
});

const handleNewImages = (e) => {
    form.new_images = Array.from(e.target.files);
};

const toggleSize = (sizeId) => {
    const index = form.variants.findIndex(v => v.size_id === sizeId);
    if (index > -1) {
        form.variants.splice(index, 1);
    } else {
        form.variants.push({ size_id: sizeId, stock: 1, sku: '' });
    }
};

const isSizeSelected = (sizeId) => form.variants.some(v => v.size_id === sizeId);

const deleteImage = (imageId) => {
    if (confirm('Видалити це фото?')) {
        router.delete(route('admin.products.images.destroy', imageId));
    }
};

const submit = () => {
    form.post(route('admin.products.update', props.product.id));
};
</script>

<template>
<!-- Встановлюємо заголовок вкладки браузера -->
    <Head title="Редагування товару" />
<AdminLayout>
    <div class="min-h-screen bg-slate-950 text-slate-100 p-6 md:p-8 font-sans">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-800">
                <div>
                    <h1 class="text-2xl font-bold text-white">Редагування товару: {{ product.title }}</h1>
                </div>
                <Link :href="route('admin.products.index')" class="text-xs font-semibold text-slate-400 hover:text-white">
                    ← Назад
                </Link>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <!-- Основна інформація -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-6">
                    <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Основні дані</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Назва</label>
                            <input v-model="form.title" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Slug</label>
                            <input v-model="form.slug" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Категорія</label>
                            <select v-model="form.category_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white">
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Бренд</label>
                            <select v-model="form.brand_id" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white">
                                <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Ціна (грн)</label>
                            <input v-model="form.price" type="number" step="0.01" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white" />
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-slate-400 mb-2">Стара ціна (грн)</label>
                            <input v-model="form.old_price" type="number" step="0.01" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-4 py-2.5 text-sm text-white" />
                        </div>
                    </div>
                </div>

                <!-- 🖼️ Наявні фото та додавання нових -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                    <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Галерея фотографій</h2>

                    <!-- Існуючі фото -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                        <div v-for="img in product.images" :key="img.id" class="relative group aspect-square rounded-lg overflow-hidden border border-slate-800 bg-slate-950">
                            <img :src="img.path" class="w-full h-full object-cover" />

                            <!-- Выбор главного фото -->
                            <label class="absolute top-1 left-1 bg-slate-950/80 p-1 rounded text-[10px] text-amber-400 cursor-pointer">
                                <input type="radio" name="main_image" :value="img.id" v-model="form.main_image_id" class="mr-1" />
                                Головна
                            </label>

                            <!-- Удаление фото -->
                            <button type="button" @click="deleteImage(img.id)" class="absolute top-1 right-1 bg-rose-600 hover:bg-rose-500 text-white p-1 rounded text-[10px]">
                                ✕
                            </button>
                        </div>
                    </div>

                    <!-- Завантаження нових фото -->
                    <div class="pt-4 border-t border-slate-800">
                        <label class="block text-xs uppercase text-slate-400 mb-2">Додати нові фото</label>
                        <input type="file" multiple accept="image/*" @change="handleNewImages" class="block w-full text-xs text-slate-400 file:py-2 file:px-4 file:rounded file:bg-slate-800 file:text-amber-400" />
                    </div>
                </div>

                <!-- 👟 Розміри -->
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
                    <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">Розміри та залишки</h2>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="s in sizes"
                            :key="s.id"
                            type="button"
                            @click="toggleSize(s.id)"
                            :class="isSizeSelected(s.id) ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-400'"
                            class="px-3.5 py-2 rounded-lg border border-slate-800 text-xs"
                        >
                            {{ s.value }} EU
                        </button>
                    </div>

                    <div v-if="form.variants.length > 0" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div v-for="v in form.variants" :key="v.size_id" class="p-3 bg-slate-950 border border-slate-800 rounded flex justify-between items-center">
                            <span class="text-xs font-bold text-amber-400">{{ sizes.find(s => s.id === v.size_id)?.value }} EU</span>
                            <input v-model.number="v.stock" type="number" min="0" class="w-20 bg-slate-900 border border-slate-800 rounded px-2 py-1 text-xs text-white text-center" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="submit" class="px-8 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg text-sm">
                        Оновити товар
                    </button>
                </div>
            </form>
        </div>
    </div>
</AdminLayout>
</template>
