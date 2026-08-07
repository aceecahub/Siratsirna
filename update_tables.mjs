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

    // 1. Update primary button
    content = content.replace(
        /rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600.*?hover:to-teal-500/g,
        'rounded-md bg-primary hover:bg-primary/90'
    );
    content = content.replace(/shadow-md shadow-emerald-600\/20/g, 'shadow-sm');
    
    // 2. Remove thick rounded corners
    content = content.replace(/rounded-2xl/g, 'rounded-lg');
    content = content.replace(/rounded-xl/g, 'rounded-md');

    // 3. Update table borders and shadows
    content = content.replace(/border-slate-200\/80/g, 'border-slate-200');
    // We keep shadow-sm since it's already minimal.

    // 4. Loosen table row padding
    content = content.replace(/py-3 text-sm/g, 'py-4 text-sm');
    
    // 5. Update pagination active state
    content = content.replace(
        /bg-emerald-600 text-white border-emerald-600/g,
        'bg-primary text-white border-primary font-bold'
    );
    content = content.replace(
        /hover:bg-emerald-50/g,
        'hover:bg-primary/5'
    );

    // 6. Generic emerald to primary replacements for text and text-badges
    content = content.replace(/text-emerald-600/g, 'text-primary');
    content = content.replace(/text-emerald-700/g, 'text-primary');
    content = content.replace(/text-emerald-400/g, 'text-primary');
    content = content.replace(/text-emerald-800/g, 'text-primary');
    content = content.replace(/border-emerald-300/g, 'border-primary/30');
    content = content.replace(/bg-emerald-50/g, 'bg-primary/5');
    content = content.replace(/bg-emerald-100/g, 'bg-primary/10');
    content = content.replace(/dark:bg-emerald-950\/60/g, 'dark:bg-primary/10');
    content = content.replace(/dark:border-emerald-700/g, 'dark:border-primary/20');
    content = content.replace(/dark:bg-emerald-950/g, 'dark:bg-primary/20');
    content = content.replace(/ring-emerald-500\/20/g, 'ring-primary/20');
    content = content.replace(/focus:border-emerald-500/g, 'focus:border-primary');
    content = content.replace(/focus:ring-emerald-500/g, 'focus:ring-primary');

    // 7. Update active text in pagination or rows
    content = content.replace(/text-emerald-600/g, 'text-primary');
    
    fs.writeFileSync(file, content);
});

console.log("Index.vue files updated successfully.");
