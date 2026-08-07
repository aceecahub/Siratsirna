import fs from 'fs';
import path from 'path';

const files = [
    'DomicileLetterPreview.vue',
    'PovertyLetterPreview.vue',
    'MarriageLetterPreview.vue',
    'KtpLetterPreview.vue',
    'LossLetterPreview.vue',
    'IncomeLetterPreview.vue',
    'HeirLetterPreview.vue',
    'AssistanceLetterPreview.vue',
    'LetterPreview.vue' // OutgoingMail
];

files.forEach(file => {
    const filePath = path.join('resources/js/Components', file);
    if (!fs.existsSync(filePath)) return;
    
    let content = fs.readFileSync(filePath, 'utf8');

    // 1. Add scaledWidth computed property if missing
    if (!content.includes('const scaledWidth =')) {
        content = content.replace(
            /const scaledHeight = computed\(\(\) => sheetHeightPx\.value \* scale\.value\);/g,
            'const scaledHeight = computed(() => sheetHeightPx.value * scale.value);\nconst scaledWidth = computed(() => sheetWidthPx * scale.value);'
        );
        // Also sometimes sheetWidthPx is sheetWidthPx.value if it's computed. Wait, in Domicile it is:
        // const sheetWidthPx = SHEET_W_MM * MM_TO_PX; (so it's a constant)
        // Let's ensure it handles both cases. If it's a ref, it needs .value.
        // Actually, in Vue setup, sheetWidthPx is const sheetWidthPx = SHEET_W_MM * MM_TO_PX; 
    }

    // 2. Change the wrapper style and class
    // From: class="letter-scale-wrap w-full flex justify-center overflow-hidden" (or whatever it is now)
    // To: class="letter-scale-wrap mx-auto"
    content = content.replace(
        /class="letter-scale-wrap[^"]*"/g,
        'class="letter-scale-wrap mx-auto relative"'
    );

    // 3. Update wrapper :style to include width
    // :style="{ height: scaled ? `${scaledHeight}px` : 'auto' }" -> :style="{ height: scaled ? `${scaledHeight}px` : 'auto', width: scaled ? `${scaledWidth}px` : '100%' }"
    // Wait, the style string can have newlines. Let's use regex to replace the whole :style block of the wrapper.
    // We can just find the `<div\n        ref="wrapper"` block.
    // Instead of complex regex, let's just replace `height: scaled ? \`\${scaledHeight}px\` : 'auto'`
    // with `height: scaled ? \`\${scaledHeight}px\` : 'auto', width: scaled ? \`\${scaledWidth}px\` : '100%'`
    content = content.replace(
        /height:\s*scaled\s*\?\s*`\$\{scaledHeight\}px`\s*:\s*'auto',?(?![\s\S]*width:\s*scaled)/g,
        "height: scaled ? `${scaledHeight}px` : 'auto',\n            width: scaled ? `${scaledWidth}px` : '100%',"
    );

    // 4. Change transformOrigin: 'top center' to 'top left'
    content = content.replace(/transformOrigin:\s*'top center'/g, "transformOrigin: 'top left'");

    fs.writeFileSync(filePath, content);
});

console.log("Preview layouts properly constrained.");
