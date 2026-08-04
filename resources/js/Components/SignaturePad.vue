<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    height: {
        type: Number,
        default: 170,
    },
    lineWidth: {
        type: Number,
        default: 2.5,
    },
    color: {
        type: String,
        default: '#1f2937',
    },
    placeholder: {
        type: String,
        default: 'Gambar tanda tangan Anda di sini (mouse / sentuh layar)',
    },
});

const emit = defineEmits(['change']);

const canvasRef = ref(null);
const hasInk = ref(false);
const drawing = ref(false);

let ctx = null;
let strokes = [];
let currentStroke = [];
let observer = null;

function getContext() {
    const canvas = canvasRef.value;

    if (!canvas) return null;

    const rect = canvas.getBoundingClientRect();
    const dpr = Math.max(window.devicePixelRatio || 1, 2);

    canvas.width = Math.round(rect.width * dpr);
    canvas.height = Math.round(props.height * dpr);

    const context = canvas.getContext('2d');
    context.scale(dpr, dpr);
    context.lineWidth = props.lineWidth;
    context.lineCap = 'round';
    context.lineJoin = 'round';
    context.strokeStyle = props.color;

    return context;
}

function redraw() {
    const canvas = canvasRef.value;

    if (!canvas) return;

    const context = ctx ?? getContext();

    if (!context) return;

    ctx = context;

    context.clearRect(0, 0, canvas.width, canvas.height);

    for (const stroke of strokes) {
        if (stroke.length === 1) {
            drawDot(stroke[0]);

            continue;
        }

        context.beginPath();
        context.moveTo(stroke[0].x, stroke[0].y);

        for (let i = 1; i < stroke.length; i++) {
            context.lineTo(stroke[i].x, stroke[i].y);
        }

        context.stroke();
    }
}

function drawDot(point) {
    const context = ctx ?? getContext();

    if (!context) return;

    ctx = context;
    context.beginPath();
    context.arc(point.x, point.y, props.lineWidth / 2, 0, Math.PI * 2);
    context.fillStyle = props.color;
    context.fill();
}

function drawSegment(from, to) {
    const context = ctx ?? getContext();

    if (!context) return;

    ctx = context;
    context.beginPath();
    context.moveTo(from.x, from.y);
    context.lineTo(to.x, to.y);
    context.stroke();
}

function getPos(event) {
    const rect = canvasRef.value.getBoundingClientRect();

    return {
        x: event.clientX - rect.left,
        y: event.clientY - rect.top,
    };
}

function onPointerDown(event) {
    event.preventDefault();
    canvasRef.value.setPointerCapture(event.pointerId);

    drawing.value = true;
    currentStroke = [getPos(event)];
    drawDot(currentStroke[0]);
}

function onPointerMove(event) {
    if (!drawing.value) return;

    event.preventDefault();

    const point = getPos(event);
    drawSegment(currentStroke[currentStroke.length - 1], point);
    currentStroke.push(point);
}

function onPointerUp() {
    if (!drawing.value) return;

    drawing.value = false;

    if (currentStroke.length > 0) {
        strokes = [...strokes, currentStroke];
    }

    currentStroke = [];
    emitChange();
}

function emitChange() {
    const canvas = canvasRef.value;

    hasInk.value = strokes.length > 0;

    if (!canvas || strokes.length === 0) {
        emit('change', null, null);

        return;
    }

    const dataUrl = canvas.toDataURL('image/png');

    canvas.toBlob((blob) => {
        if (!blob) {
            emit('change', null, dataUrl);

            return;
        }

        const file = new File([blob], 'tanda-tangan.png', {
            type: 'image/png',
        });

        emit('change', file, dataUrl);
    }, 'image/png');
}

function clearSignature() {
    strokes = [];
    currentStroke = [];
    hasInk.value = false;
    redraw();
    emitChange();
}

function undo() {
    strokes = strokes.slice(0, -1);
    currentStroke = [];
    redraw();
    emitChange();
}

function resetCanvas() {
    strokes = [];
    currentStroke = [];
    hasInk.value = false;
    redraw();
}

onMounted(() => {
    redraw();

    observer = new ResizeObserver(() => {
        if (!canvasRef.value) return;

        redraw();
    });

    observer.observe(canvasRef.value);
});

onBeforeUnmount(() => {
    observer?.disconnect();
});

defineExpose({ clearSignature });
</script>

<template>
    <div>
        <div
            class="relative overflow-hidden rounded-xl border border-slate-300 bg-white shadow-inner dark:border-slate-700"
            :style="{ height: `${height}px` }"
        >
            <canvas
                ref="canvasRef"
                class="block h-full w-full cursor-crosshair touch-none select-none"
                style="touch-action: none"
                @pointerdown="onPointerDown"
                @pointermove="onPointerMove"
                @pointerup="onPointerUp"
                @pointercancel="onPointerUp"
            />
            <div
                v-if="!hasInk"
                class="pointer-events-none absolute inset-0 flex items-center justify-center px-4 text-center text-xs font-medium text-slate-400"
            >
                {{ placeholder }}
            </div>
        </div>

        <div class="mt-2 flex items-center gap-2">
            <button
                type="button"
                @click="undo"
                :disabled="!hasInk"
                class="inline-flex min-h-[2.5rem] items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
            >
                Undo (Batal)
            </button>
            <button
                type="button"
                @click="clearSignature"
                :disabled="!hasInk"
                class="inline-flex min-h-[2.5rem] items-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-red-950 dark:bg-slate-800 dark:text-red-400"
            >
                Bersihkan Canvas
            </button>
        </div>
    </div>
</template>
