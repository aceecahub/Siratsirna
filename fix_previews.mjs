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

    // 1. Update the wrapper to flex justify-center and hidden overflow
    // Find: class="letter-scale-wrap"
    // We want to add w-full flex justify-center overflow-hidden to wrapper if not present.
    // Wait, let's just do a string replace on the exact wrapper block.
    // The wrapper block usually looks like:
    /*
    <div
        ref="wrapper"
        class="letter-scale-wrap"
        :style="{
            height: scaled ? `${scaledHeight}px` : 'auto',
        }"
    >
    */
    content = content.replace(
        /class="letter-scale-wrap"/g,
        'class="letter-scale-wrap w-full flex justify-center overflow-hidden"'
    );
    
    // Also some wrappers might have mx-auto on the paper.
    // Let's remove mx-auto from the paper to let flex justify-center do the work.
    content = content.replace(/class="print-sheet relative mx-auto/g, 'class="print-sheet relative');
    
    // Make sure we have the rounded corners on the paper in web view to make it look nicer inside the grey box
    // Wait, the grey box has the rounded corners. 
    
    fs.writeFileSync(filePath, content);
});

console.log("Preview wrappers updated.");
