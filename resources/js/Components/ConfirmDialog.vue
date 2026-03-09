<script setup>
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmar Ação',
    },
    message: {
        type: String,
        default: 'Tem certeza que deseja realizar esta ação?',
    },
    confirmText: {
        type: String,
        default: 'Confirmar',
    },
    cancelText: {
        type: String,
        default: 'Cancelar',
    },
    type: {
        type: String,
        default: 'danger', // 'danger' ou 'primary'
    }
});

const isProcessing = ref(false);

const emit = defineEmits(['close', 'confirm']);

const close = () => {
    if (isProcessing.value) return;
    emit('close');
};

const confirm = () => {
    isProcessing.value = true;
    emit('confirm', {
        onSuccess: () => {
            isProcessing.value = false;
            emit('close');
        },
        onError: () => {
            isProcessing.value = false;
            emit('close');
        },
        onFinish: () => {
            isProcessing.value = false;
            emit('close');
        }
    });
};
</script>

<template>
    <Modal :show="show" @close="close" maxWidth="sm" position="center">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ title }}
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="close" :disabled="isProcessing">
                    {{ cancelText }}
                </SecondaryButton>

                <DangerButton v-if="type === 'danger'" :class="{ 'opacity-25': isProcessing }" :disabled="isProcessing" @click="confirm">
                    <span v-if="isProcessing">Processando...</span>
                    <span v-else>{{ confirmText }}</span>
                </DangerButton>
                
                <PrimaryButton v-if="type === 'primary'" :class="{ 'opacity-25': isProcessing }" :disabled="isProcessing" @click="confirm">
                    <span v-if="isProcessing">Processando...</span>
                    <span v-else>{{ confirmText }}</span>
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
