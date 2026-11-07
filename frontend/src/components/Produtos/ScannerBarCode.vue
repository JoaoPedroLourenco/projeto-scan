<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode'
import { useProduct } from '../../composables/useProduct.js'
import { lookupBarCode } from '../../services/productService.js'
import CameraSelector from './CameraSelector.vue'

const { definirProduto } = useProduct()

const emit = defineEmits(['codigo-lido'])

const readCode = ref('')
const activeScanner = ref(false)
const erro = ref('')

const cameras = ref([])
const selectedCamera = ref('')

let scanner = null
let processingRead = false

async function changeCamera() {
  if (!activeScanner.value) return

  try {
    await stopScanner()
    await startScanner()
  } catch (error) {
    console.error('Erro ao trocar câmera:', error)
    erro.value = 'Não foi possível trocar a câmera.'
  }
}

async function selectCamera(cameraId) {
  selectedCamera.value = cameraId
  await changeCamera()
}

// Busca as câmeras disponíveis
async function loadCameras() {
  try {
    const response = await Html5Qrcode.getCameras()

    cameras.value = response

    // Define a primeira câmera como padrão
    if (!selectedCamera.value && cameras.value.length > 0) {
      selectedCamera.value = cameras.value[0].id
    }
  } catch (error) {
    erro.value = 'Não foi possível identificar as câmeras.'
    console.error(error)
  }
}

// Inicializa o scanner
async function startScanner() {
  try {
    erro.value = ''

    if (cameras.value.length === 0) {
      await loadCameras()
    }

    if (!scanner) {
      scanner = new Html5Qrcode('reader', {
        formatsToSupport: [
          Html5QrcodeSupportedFormats.EAN_13,
          Html5QrcodeSupportedFormats.EAN_8,
          Html5QrcodeSupportedFormats.UPC_A,
          Html5QrcodeSupportedFormats.UPC_E,
        ],
        verbose: false,
      })
    }

    const camera = selectedCamera.value || cameras.value[0]?.id

    if (!camera) {
      erro.value = 'Nenhuma câmera disponível.'
      return
    }

    processingRead = false

    await scanner.start(
      camera,
      {
        fps: 30,
        qrbox: { width: 280, height: 150 },
      },
      async (barcode) => {
        if (processingRead) return

        processingRead = true

        if (barcode) {
          stopScanner()
        }

        try {
          const response = await lookupBarCode(barcode)

          readCode.value = barcode

          emit('codigo-lido', barcode)

          definirProduto(response.data)
        } catch (error) {
          console.error('Erro ao buscar produto:', error)
          erro.value = 'Não foi possível consultar o produto.'

          processingRead = false
        }
      },
    )

    activeScanner.value = true
  } catch (error) {
    erro.value = 'Não foi possível abrir a câmera.'
    console.error('Erro ao iniciar scanner:', error)
  }
}

// Encerra o scanner
async function stopScanner() {
  try {
    if (scanner?.isScanning) {
      await scanner.stop()
    }
  } catch (error) {
    console.error('Erro ao parar scanner:', error)
  } finally {
    activeScanner.value = false
  }
}

// Desliga a câmera antes de desmontar o componente
onBeforeUnmount(async () => {
  if (scanner?.isScanning) {
    await scanner.stop()
  }

  scanner?.clear()
})
</script>

<template>
  <section>
    <h2>Leitor de produtos</h2>

    <div v-if="scanner">
      <CameraSelector
        :cameras="cameras"
        :model-value="selectedCamera"
        @update:model-value="selectCamera"
      />
    </div>

    <div id="reader"></div>

    <div>
      <button v-if="!activeScanner" type="button" @click="startScanner">Abrir câmera</button>

      <button v-else type="button" @click="stopScanner">Parar câmera</button>
    </div>

    <p v-if="erro">
      {{ erro }}
    </p>
  </section>
</template>
