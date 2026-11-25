<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode'
import { useProduct } from '../../composables/useProduct.js'
import { lookupBarCode } from '../../services/productService.js'
import CameraSelector from './CameraSelector.vue'

const emit = defineEmits(['codigo-lido'])
const { definirProduto } = useProduct()
const cameras = ref([])
const selectedCamera = ref('')
const active = ref(false)
const loading = ref(false)
const error = ref('')
const menuLoading = ref(false)
let scanner
let reading = false

async function loadCameras() {
  menuLoading.value = true
  try {
    cameras.value = await Html5Qrcode.getCameras()
    selectedCamera.value ||= cameras.value[0]?.id || ''
  } catch (cause) {
    error.value = 'Não foi possível listar as câmeras. Verifique a permissão do navegador.'
  } finally {
    menuLoading.value = false
  }
}

async function startCamera() {
  if (active.value || loading.value) return
  loading.value = true
  error.value = ''

  try {
    if (!cameras.value.length) await loadCameras()
    if (!selectedCamera.value) {
      error.value ||= 'Nenhuma câmera foi encontrada.'
      return
    }

    scanner ||= new Html5Qrcode('barcode-reader', {
      formatsToSupport: [
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
      ],
      verbose: false,
    })
    reading = false
    await scanner.start(
      selectedCamera.value,
      { fps: 10, qrbox: { width: 280, height: 150 } },
      async (code) => {
        if (reading) return
        reading = true
        await stopCamera()
        emit('codigo-lido', code)
        try {
          const response = await lookupBarCode(code)
          definirProduto(response.data)
        } catch (cause) {
          error.value = 'Código lido, mas não foi possível consultar o produto.'
          reading = false
        }
      },
    )
    active.value = true
  } catch (cause) {
    error.value = 'Não foi possível iniciar a câmera. Confira a permissão do navegador.'
    console.error('Erro ao iniciar scanner:', cause)
  } finally {
    loading.value = false
  }
}

async function stopCamera() {
  try {
    if (scanner?.isScanning) await scanner.stop()
  } catch (cause) {
    console.error('Erro ao parar scanner:', cause)
  } finally {
    active.value = false
  }
}

async function selectCamera(cameraId) {
  selectedCamera.value = cameraId
  if (active.value) {
    await stopCamera()
    await startCamera()
  }
}

onBeforeUnmount(async () => {
  await stopCamera()
  scanner?.clear()
})
</script>

<template>
  <section class="space-y-3">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <CameraSelector
          :cameras="cameras"
          :model-value="selectedCamera"
          :disabled="loading || menuLoading"
          :loading="menuLoading"
          @open="!cameras.length && loadCameras()"
          @update:model-value="selectCamera"
        />
        <button
          v-if="!active"
          type="button"
          :disabled="loading"
          class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
          @click="startCamera"
        >
          {{ loading ? 'Iniciando...' : 'Iniciar câmera' }}
        </button>
        <button
          v-else
          type="button"
          class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
          @click="stopCamera"
        >
          Parar câmera
        </button>
      </div>
    </div>

    <div id="barcode-reader" class="w-full"></div>

    <p v-if="error" class="text-xs text-rose-600" role="alert">{{ error }}</p>
  </section>
</template>
