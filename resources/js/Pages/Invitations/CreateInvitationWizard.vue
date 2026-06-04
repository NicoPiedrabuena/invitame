<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';


const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    invitation: {
        type: Object,
        default: null,
    },
});

const isEditing = computed(() => props.mode === 'edit' && props.invitation);

const fontOptions = [
    { label: 'Serif clásica', value: 'classic_serif' },
    { label: 'Sans moderna', value: 'modern_sans' },
    { label: 'Elegante editorial', value: 'editorial_serif' },
    { label: 'Cursiva romántica', value: 'romantic_script' },
    { label: 'Negrita display', value: 'display_bold' },
];

const fontFamilyMap = {
    classic_serif: 'Georgia, Cambria, "Times New Roman", Times, serif',
    modern_sans: '"Trebuchet MS", "Segoe UI", Tahoma, Verdana, sans-serif',
    editorial_serif: '"Palatino Linotype", Palatino, "Book Antiqua", serif',
    romantic_script: '"Brush Script MT", "Lucida Handwriting", cursive',
    display_bold: '"Arial Black", Gadget, sans-serif',
};

const steps = [
    { id: 1, title: 'Portada', description: 'Título y subtítulo' },
    { id: 2, title: 'Cuenta regresiva', description: 'Fecha y hora del evento' },
    { id: 3, title: 'Ubicación', description: 'Lugar, dirección y mapa' },
    { id: 4, title: 'Dress code', description: 'Descripción e imágenes' },
    { id: 5, title: 'Regalos', description: 'Mensaje y cuenta bancaria' },
    { id: 6, title: 'Fotos', description: 'Link de Google Drive' },
    { id: 7, title: 'Música', description: 'Embed de Spotify' },
    { id: 8, title: 'Muro', description: 'Mensajes de invitados' },
    { id: 9, title: 'RSVP', description: 'Confirmaciones e invitados' },
];

const defaultThemeSettings = {
    section_backgrounds: {
        portada: null,
        cuenta_regresiva: null,
        ubicacion: null,
        dress_code: null,
        regalos: null,
        fotos: null,
        musica: null,
        muro: null,
        rsvp: null,
    },
    typography: {
        global: {
            primary: {
                font_family: 'classic_serif',
                color: '#0f172a',
            },
            secondary: {
                font_family: 'modern_sans',
                color: '#334155',
            },
        },
    },
};

const form = useForm({
    title: props.invitation?.title ?? '',
    subtitle: props.invitation?.subtitle ?? '',
    event_date: props.invitation?.event_date
        ? props.invitation.event_date.slice(0, 16)
        : '',
    venue_name: props.invitation?.venue_name ?? '',
    address: props.invitation?.address ?? '',
    google_maps_url: props.invitation?.google_maps_url ?? '',
    dress_code_description:
        props.invitation?.dress_code_description
        ?? props.invitation?.dress_code
        ?? '',
    dress_code_allowed_images: [],
    dress_code_not_allowed_images: [],
    gifts_message: props.invitation?.gifts_message ?? '',
    bank_alias: props.invitation?.bank_alias ?? '',
    drive_photos_url: props.invitation?.drive_photos_url ?? '',
    spotify_iframe_code: props.invitation?.spotify_iframe_code ?? '',
    message_wall_enabled: Boolean(props.invitation?.message_wall_enabled ?? false),
    rsvp_deadline: props.invitation?.rsvp_deadline
        ? props.invitation.rsvp_deadline.slice(0, 16)
        : '',
    rsvp_companions: props.invitation?.rsvp_companions ?? [],
    rsvp_message: props.invitation?.rsvp_message ?? '',
    theme_settings: props.invitation?.theme_settings ?? defaultThemeSettings,

    background_portada: null,
    background_cuenta_regresiva: null,
    background_ubicacion: null,
    background_dress_code: null,
    background_regalos: null,
    background_fotos: null,
    background_musica: null,
    background_muro: null,
    background_rsvp: null,
});

const existingSectionBackgrounds = computed(() => props.invitation?.theme_settings?.section_backgrounds || {});
const existingAllowedDressCodeImages = computed(() => props.invitation?.dress_code_allowed_images || []);
const existingNotAllowedDressCodeImages = computed(() => props.invitation?.dress_code_not_allowed_images || []);

const resolvedImageUrl = (path) => {
    if (!path) {
        return null;
    }

    return path.startsWith('http') ? path : `/storage/${path}`;
};

const currentStep = ref(1);
const hasEnteredWizard = ref(isEditing.value);
const allowedPreviewUrls = ref([]);
const notAllowedPreviewUrls = ref([]);
const backgroundPreviewUrls = ref({
    portada: resolvedImageUrl(existingSectionBackgrounds.value.portada),
    cuenta_regresiva: resolvedImageUrl(existingSectionBackgrounds.value.cuenta_regresiva),
    ubicacion: resolvedImageUrl(existingSectionBackgrounds.value.ubicacion),
    dress_code: resolvedImageUrl(existingSectionBackgrounds.value.dress_code),
    regalos: resolvedImageUrl(existingSectionBackgrounds.value.regalos),
    fotos: resolvedImageUrl(existingSectionBackgrounds.value.fotos),
    musica: resolvedImageUrl(existingSectionBackgrounds.value.musica),
    muro: resolvedImageUrl(existingSectionBackgrounds.value.muro),
    rsvp: resolvedImageUrl(existingSectionBackgrounds.value.rsvp),
});

const currentStepInfo = computed(() => steps[currentStep.value - 1]);
const progressPercent = computed(() => (currentStep.value / steps.length) * 100);
const isFirstStep = computed(() => currentStep.value === 1);
const isLastStep = computed(() => currentStep.value === steps.length);
const canShowBankButton = computed(() => form.bank_alias.trim().length > 0);
const hasErrors = computed(() => Object.keys(form.errors).length > 0);
const primaryPreviewStyle = computed(() => {
    const primary = form.theme_settings.typography.global.primary;

    return {
        fontFamily: fontFamilyMap[primary.font_family] || fontFamilyMap.classic_serif,
        color: primary.color || '#0f172a',
    };
});
const secondaryPreviewStyle = computed(() => {
    const secondary = form.theme_settings.typography.global.secondary;

    return {
        fontFamily: fontFamilyMap[secondary.font_family] || fontFamilyMap.modern_sans,
        color: secondary.color || '#334155',
    };
});

const getFontLabel = (fontValue) => fontOptions.find((font) => font.value === fontValue)?.label || 'Sin definir';

const revokeUrl = (url) => {
    if (url && url.startsWith('blob:')) {
        URL.revokeObjectURL(url);
    }
};

const revokeUrls = (urls) => {
    urls.forEach((url) => revokeUrl(url));
};

const syncPreviewFiles = (event, field, previewStore) => {
    const files = Array.from(event.target.files || []);

    revokeUrls(previewStore.value);
    previewStore.value = files.map((file) => URL.createObjectURL(file));
    form[field] = files;
};

const syncBackgroundFile = (event, field, sectionKey) => {
    const file = event.target.files?.[0] ?? null;
    const previousPreview = backgroundPreviewUrls.value[sectionKey];

    revokeUrl(previousPreview);

    if (!file) {
        backgroundPreviewUrls.value[sectionKey] = null;
        form[field] = null;
        return;
    }

    form[field] = file;
    backgroundPreviewUrls.value[sectionKey] = URL.createObjectURL(file);
};

const addCompanion = () => {
    form.rsvp_companions.push({
        name: '',
        dietary_restrictions: '',
    });
};

const removeCompanion = (index) => {
    form.rsvp_companions.splice(index, 1);
};

const enterWizard = () => {
    hasEnteredWizard.value = true;
};

const nextStep = () => {
    if (currentStep.value < steps.length) {
        currentStep.value += 1;
        return;
    }

    submit();
};

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value -= 1;
    }
};

const submit = () => {
    if (isEditing.value) {
        form
            .transform((data) => ({
                ...data,
                _method: 'put',
            }))
            .post(route('invitations.update', props.invitation.id), {
                preserveScroll: true,
                forceFormData: true,
            });

        return;
    }

    form.post(route('invitations.store'), {
        preserveScroll: true,
        forceFormData: true,
    });
};

onBeforeUnmount(() => {
    revokeUrls(allowedPreviewUrls.value);
    revokeUrls(notAllowedPreviewUrls.value);
    Object.values(backgroundPreviewUrls.value).forEach((url) => {
        revokeUrl(url);
    });
});
</script>

<template>
    <Head :title="isEditing ? 'Editar invitación' : 'Crear invitación'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-xl font-semibold leading-tight text-gray-800">
    {{ isEditing ? 'Editar invitación' : 'Crear invitación' }}
</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Paso {{ currentStep }} de {{ steps.length }}</span>
            </div>
        </template>

        <div class="py-4">
            <div class="mx-auto max-w-md px-4 pb-28">
                <div v-if="!hasEnteredWizard" class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 p-6 text-white shadow-xl">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Creador de invitaciones</p>
                    <h3 class="mt-3 text-2xl font-semibold leading-tight">Ingresá y comenzá a crear tu invitación</h3>
                    <p class="mt-3 text-sm text-slate-300">Cuando presiones Ingresar se abrirá el asistente paso a paso.</p>

                    <button
                        type="button"
                        class="mt-6 flex w-full items-center justify-center rounded-2xl bg-amber-400 px-5 py-4 text-base font-semibold text-slate-950 transition active:scale-[0.99]"
                        @click="enterWizard"
                    >
                        Ingresar
                    </button>
                </div>

                <template v-else>
                <div class="mb-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Wizard móvil</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ currentStepInfo.title }}</h3>
                            <p class="text-sm text-slate-500">{{ currentStepInfo.description }}</p>
                        </div>
                        <span class="rounded-2xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">{{ currentStep }}</span>
                    </div>

                    <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-amber-400 transition-all" :style="{ width: `${progressPercent}%` }" />
                    </div>
                </div>

                <form class="space-y-4" @submit.prevent="submit">
                    <section v-if="currentStep === 1" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="space-y-2">
                            <p class="text-sm font-semibold text-slate-900">Texto global de portada</p>
                            <p class="text-xs text-slate-500">Cada bloque reúne input, estilo y preview para evitar scroll.</p>
                        </div>

                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-900">Título</p>
                                <span class="rounded-full bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-amber-800">Global principal</span>
                            </div>

                            <div>
                                <InputLabel for="title" value="Texto del título" />
                                <TextInput id="title" v-model="form.title" class="mt-2 block w-full" placeholder="Nos casamos" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>

                            <div>
                                <InputLabel for="global_primary_font" value="Fuente del título" />
                                <select id="global_primary_font" v-model="form.theme_settings.typography.global.primary.font_family" class="mt-2 block w-full rounded-2xl border-slate-300 bg-white p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">
                                    <option v-for="font in fontOptions" :key="`title-${font.value}`" :value="font.value">{{ font.label }}</option>
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Actual: {{ getFontLabel(form.theme_settings.typography.global.primary.font_family) }}</p>
                            </div>

                            <div>
                                <InputLabel for="global_primary_color" value="Color del título" />
                                <input id="global_primary_color" v-model="form.theme_settings.typography.global.primary.color" type="color" class="mt-2 block h-12 w-full rounded-2xl border border-slate-300 bg-white p-1" />
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Preview título</p>
                                <h4 class="mt-2 text-4xl font-bold leading-tight" :style="primaryPreviewStyle">{{ form.title || 'Nos casamos' }}</h4>
                            </div>
                        </div>

                        <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-900">Subtítulo</p>
                                <span class="rounded-full bg-sky-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-sky-800">Global secundaria</span>
                            </div>

                            <div>
                                <InputLabel for="subtitle" value="Texto del subtítulo" />
                                <TextInput id="subtitle" v-model="form.subtitle" class="mt-2 block w-full" placeholder="Nico y Flor" />
                                <InputError class="mt-2" :message="form.errors.subtitle" />
                            </div>

                            <div>
                                <InputLabel for="global_secondary_font" value="Fuente del subtítulo" />
                                <select id="global_secondary_font" v-model="form.theme_settings.typography.global.secondary.font_family" class="mt-2 block w-full rounded-2xl border-slate-300 bg-white p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900">
                                    <option v-for="font in fontOptions" :key="`subtitle-${font.value}`" :value="font.value">{{ font.label }}</option>
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Actual: {{ getFontLabel(form.theme_settings.typography.global.secondary.font_family) }}</p>
                            </div>

                            <div>
                                <InputLabel for="global_secondary_color" value="Color del subtítulo" />
                                <input id="global_secondary_color" v-model="form.theme_settings.typography.global.secondary.color" type="color" class="mt-2 block h-12 w-full rounded-2xl border border-slate-300 bg-white p-1" />
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                <p class="text-xs uppercase tracking-wide text-slate-500">Preview subtítulo</p>
                                <p class="mt-2 text-lg" :style="secondaryPreviewStyle">{{ form.subtitle || 'Nico y Flor' }}</p>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-500">Preview combinado</p>
                            <h4 class="mt-3 text-4xl font-bold leading-tight" :style="primaryPreviewStyle">{{ form.title || 'Nos casamos' }}</h4>
                            <p class="mt-2 text-lg" :style="secondaryPreviewStyle">{{ form.subtitle || 'Nico y Flor' }}</p>
                        </div>

                        <div>
                            <InputLabel for="bg_portada" value="Fondo de portada (imagen)" />
                            <input id="bg_portada" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_portada', 'portada')" />
                            <InputError class="mt-2" :message="form.errors.background_portada" />
                            <p v-if="backgroundPreviewUrls.portada" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.portada" :src="backgroundPreviewUrls.portada" alt="Vista previa portada" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 2" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="event_date" value="Fecha y hora exacta" />
                            <TextInput id="event_date" v-model="form.event_date" type="datetime-local" class="mt-2 block w-full" />
                            <InputError class="mt-2" :message="form.errors.event_date" />
                        </div>

                        <div>
                            <InputLabel for="bg_cuenta" value="Fondo de cuenta regresiva (imagen)" />
                            <input id="bg_cuenta" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_cuenta_regresiva', 'cuenta_regresiva')" />
                            <InputError class="mt-2" :message="form.errors.background_cuenta_regresiva" />
                            <p v-if="backgroundPreviewUrls.cuenta_regresiva" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.cuenta_regresiva" :src="backgroundPreviewUrls.cuenta_regresiva" alt="Vista previa cuenta" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 3" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="venue_name" value="Nombre del lugar" />
                            <TextInput id="venue_name" v-model="form.venue_name" class="mt-2 block w-full" placeholder="Finca Las Rosas" />
                            <InputError class="mt-2" :message="form.errors.venue_name" />
                        </div>

                        <div>
                            <InputLabel for="address" value="Dirección" />
                            <textarea id="address" v-model="form.address" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" placeholder="Calle 123, ciudad" />
                            <InputError class="mt-2" :message="form.errors.address" />
                        </div>

                        <div>
                            <InputLabel for="google_maps_url" value="Link de Google Maps" />
                            <TextInput id="google_maps_url" v-model="form.google_maps_url" class="mt-2 block w-full" placeholder="https://maps.google.com/..." />
                            <InputError class="mt-2" :message="form.errors.google_maps_url" />
                        </div>

                        <div>
                            <InputLabel for="bg_ubicacion" value="Fondo de ubicación (imagen)" />
                            <input id="bg_ubicacion" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_ubicacion', 'ubicacion')" />
                            <InputError class="mt-2" :message="form.errors.background_ubicacion" />
                            <p v-if="backgroundPreviewUrls.ubicacion" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.ubicacion" :src="backgroundPreviewUrls.ubicacion" alt="Vista previa ubicación" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 4" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="dress_code_description" value="Descripción del dress code" />
                            <textarea id="dress_code_description" v-model="form.dress_code_description" rows="4" class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" placeholder="Elegante, colores claros, sin blanco" />
                            <InputError class="mt-2" :message="form.errors.dress_code_description" />
                        </div>

                        <div class="space-y-3">
                            <div v-if="existingAllowedDressCodeImages.length" class="space-y-2">
                                <p class="text-xs font-medium text-slate-500">Imágenes actuales permitidas</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <img v-for="image in existingAllowedDressCodeImages" :key="image" :src="resolvedImageUrl(image)" alt="Imagen permitida actual" class="h-24 w-full rounded-2xl object-cover" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="dress_code_allowed_images" value="Imágenes permitidas" />
                                <p class="mt-1 text-xs text-slate-500">Si seleccionas nuevas imágenes, reemplazarán las actuales al guardar.</p>
                                <input id="dress_code_allowed_images" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncPreviewFiles(event, 'dress_code_allowed_images', allowedPreviewUrls)" />
                                <InputError class="mt-2" :message="form.errors.dress_code_allowed_images" />
                            </div>

                            <div v-if="allowedPreviewUrls.length" class="grid grid-cols-3 gap-2">
                                <p class="col-span-3 text-xs font-medium text-slate-500">Nueva imagen seleccionada</p>
                                <img v-for="preview in allowedPreviewUrls" :key="preview" :src="preview" alt="Vista previa" class="h-24 w-full rounded-2xl object-cover" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div v-if="existingNotAllowedDressCodeImages.length" class="space-y-2">
                                <p class="text-xs font-medium text-slate-500">Imágenes actuales no permitidas</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <img v-for="image in existingNotAllowedDressCodeImages" :key="image" :src="resolvedImageUrl(image)" alt="Imagen no permitida actual" class="h-24 w-full rounded-2xl object-cover" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="dress_code_not_allowed_images" value="Imágenes no permitidas" />
                                <p class="mt-1 text-xs text-slate-500">Si seleccionas nuevas imágenes, reemplazarán las actuales al guardar.</p>
                                <input id="dress_code_not_allowed_images" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncPreviewFiles(event, 'dress_code_not_allowed_images', notAllowedPreviewUrls)" />
                                <InputError class="mt-2" :message="form.errors.dress_code_not_allowed_images" />
                            </div>

                            <div v-if="notAllowedPreviewUrls.length" class="grid grid-cols-3 gap-2">
                                <p class="col-span-3 text-xs font-medium text-slate-500">Nueva imagen seleccionada</p>
                                <img v-for="preview in notAllowedPreviewUrls" :key="preview" :src="preview" alt="Vista previa" class="h-24 w-full rounded-2xl object-cover" />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="bg_dress" value="Fondo de dress code (imagen)" />
                            <input id="bg_dress" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_dress_code', 'dress_code')" />
                            <InputError class="mt-2" :message="form.errors.background_dress_code" />
                            <p v-if="backgroundPreviewUrls.dress_code" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.dress_code" :src="backgroundPreviewUrls.dress_code" alt="Vista previa dress code" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 5" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="gifts_message" value="Mensaje de regalos" />
                            <textarea id="gifts_message" v-model="form.gifts_message" rows="4" class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" placeholder="Tu mensaje aquí..." />
                            <InputError class="mt-2" :message="form.errors.gifts_message" />
                        </div>

                        <div>
                            <InputLabel for="bank_alias" value="Alias / CBU" />
                            <TextInput id="bank_alias" v-model="form.bank_alias" class="mt-2 block w-full" placeholder="alias-banco" />
                            <InputError class="mt-2" :message="form.errors.bank_alias" />
                        </div>

                        <button v-if="canShowBankButton" type="button" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">
                            Ver cuenta
                        </button>

                        <div>
                            <InputLabel for="bg_regalos" value="Fondo de regalos (imagen)" />
                            <input id="bg_regalos" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_regalos', 'regalos')" />
                            <InputError class="mt-2" :message="form.errors.background_regalos" />
                            <p v-if="backgroundPreviewUrls.regalos" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.regalos" :src="backgroundPreviewUrls.regalos" alt="Vista previa regalos" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 6" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="drive_photos_url" value="Link de Google Drive" />
                            <TextInput id="drive_photos_url" v-model="form.drive_photos_url" class="mt-2 block w-full" placeholder="https://drive.google.com/..." />
                            <InputError class="mt-2" :message="form.errors.drive_photos_url" />
                        </div>

                        <div>
                            <InputLabel for="bg_fotos" value="Fondo de fotos (imagen)" />
                            <input id="bg_fotos" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_fotos', 'fotos')" />
                            <InputError class="mt-2" :message="form.errors.background_fotos" />
                            <p v-if="backgroundPreviewUrls.fotos" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.fotos" :src="backgroundPreviewUrls.fotos" alt="Vista previa fotos" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 7" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="spotify_iframe_code" value="Código iframe de Spotify" />
                            <textarea id="spotify_iframe_code" v-model="form.spotify_iframe_code" rows="6" class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 p-3 font-mono text-xs shadow-sm focus:border-slate-900 focus:ring-slate-900" placeholder='<iframe src="https://open.spotify.com/embed/playlist/..." />' />
                            <InputError class="mt-2" :message="form.errors.spotify_iframe_code" />
                        </div>

                        <div>
                            <InputLabel for="bg_musica" value="Fondo de música (imagen)" />
                            <input id="bg_musica" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_musica', 'musica')" />
                            <InputError class="mt-2" :message="form.errors.background_musica" />
                            <p v-if="backgroundPreviewUrls.musica" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.musica" :src="backgroundPreviewUrls.musica" alt="Vista previa música" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 8" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                            <div>
                                <p class="text-sm font-medium text-slate-900">Muro de mensajes</p>
                                <p class="text-xs text-slate-500">Activa o desactiva esta sección para invitados.</p>
                            </div>

                            <button
                                type="button"
                                class="flex h-12 w-20 items-center rounded-full p-1 transition"
                                :class="form.message_wall_enabled ? 'bg-emerald-500' : 'bg-slate-300'"
                                @click="form.message_wall_enabled = !form.message_wall_enabled"
                            >
                                <span
                                    class="h-10 w-10 rounded-full bg-white shadow-sm transition-transform"
                                    :class="form.message_wall_enabled ? 'translate-x-8' : 'translate-x-0'"
                                />
                            </button>
                        </div>

                        <div>
                            <InputLabel for="bg_muro" value="Fondo de muro (imagen)" />
                            <input id="bg_muro" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_muro', 'muro')" />
                            <InputError class="mt-2" :message="form.errors.background_muro" />
                            <p v-if="backgroundPreviewUrls.muro" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.muro" :src="backgroundPreviewUrls.muro" alt="Vista previa muro" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <section v-else-if="currentStep === 9" class="space-y-4 rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div>
                            <InputLabel for="rsvp_deadline" value="Fecha límite de confirmación" />
                            <TextInput id="rsvp_deadline" v-model="form.rsvp_deadline" type="datetime-local" class="mt-2 block w-full" />
                            <InputError class="mt-2" :message="form.errors.rsvp_deadline" />
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-slate-900">Acompañantes</h3>
                                <button type="button" class="rounded-2xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-900" @click="addCompanion">
                                    Agregar acompañante
                                </button>
                            </div>

                            <div v-if="form.rsvp_companions.length" class="space-y-3">
                                <article v-for="(companion, index) in form.rsvp_companions" :key="index" class="space-y-3 rounded-2xl border border-slate-200 p-3">
                                    <div>
                                        <InputLabel :for="`companion-name-${index}`" value="Nombre" />
                                        <TextInput :id="`companion-name-${index}`" v-model="companion.name" class="mt-2 block w-full" />
                                    </div>

                                    <div>
                                        <InputLabel :for="`companion-diet-${index}`" value="Dieta alimentaria / Restricciones" />
                                        <TextInput :id="`companion-diet-${index}`" v-model="companion.dietary_restrictions" class="mt-2 block w-full" />
                                    </div>

                                    <button type="button" class="text-left text-sm font-semibold text-rose-600" @click="removeCompanion(index)">
                                        Quitar acompañante
                                    </button>
                                </article>
                            </div>
                        </div>

                        <div>
                            <InputLabel for="rsvp_message" value="Mensaje opcional" />
                            <textarea id="rsvp_message" v-model="form.rsvp_message" rows="3" class="mt-2 block w-full rounded-2xl border-slate-300 bg-slate-50 p-3 text-sm shadow-sm focus:border-slate-900 focus:ring-slate-900" placeholder="Deja un mensaje adicional..." />
                            <InputError class="mt-2" :message="form.errors.rsvp_message" />
                        </div>

                        <div>
                            <InputLabel for="bg_rsvp" value="Fondo de RSVP (imagen)" />
                            <input id="bg_rsvp" type="file" accept="image/*" class="mt-2 block w-full text-sm" @change="(event) => syncBackgroundFile(event, 'background_rsvp', 'rsvp')" />
                            <InputError class="mt-2" :message="form.errors.background_rsvp" />
                            <p v-if="backgroundPreviewUrls.rsvp" class="mt-2 text-xs font-medium text-slate-500">Imagen actual / nueva imagen seleccionada</p>
                            <img v-if="backgroundPreviewUrls.rsvp" :src="backgroundPreviewUrls.rsvp" alt="Vista previa rsvp" class="mt-2 h-24 w-full rounded-2xl object-cover" />
                        </div>
                    </section>

                    <div v-if="hasErrors" class="rounded-3xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                        Revisa los campos marcados antes de continuar.
                    </div>

                    <div class="fixed inset-x-0 bottom-0 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur">
                        <div class="mx-auto flex max-w-md gap-3">
                            <SecondaryButton type="button" class="flex-1 justify-center py-3 text-sm" :disabled="isFirstStep" @click="previousStep">
                                Anterior
                            </SecondaryButton>

                            <PrimaryButton type="button" class="flex-1 justify-center py-3 text-sm" :disabled="form.processing" @click="nextStep">
                                {{ isLastStep ? (isEditing ? 'Guardar cambios' : 'Finalizar') : 'Siguiente' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
