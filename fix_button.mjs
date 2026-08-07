import fs from 'fs';
import path from 'path';

const directories = [
    'OutgoingMail', 'IncomingMail', 'DomicileLetter', 'PovertyLetter',
    'MarriageLetter', 'KtpLetter', 'LossLetter', 'IncomeLetter',
    'HeirLetter', 'AssistanceLetter'
];

directories.forEach(dir => {
    const file = path.join('resources/js/Pages', dir, 'Index.vue');
    if (!fs.existsSync(file)) return;
    
    let content = fs.readFileSync(file, 'utf8');

    // Fix the swallowed button classes
    content = content.replace(
        /rounded-md bg-primary hover:bg-primary\/90 active:scale-95/g,
        'rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90 active:scale-95'
    );
    
    fs.writeFileSync(file, content);
});

console.log("Fixed button classes successfully.");
