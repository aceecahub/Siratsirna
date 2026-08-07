import fs from 'fs';
import path from 'path';

const directories = [
    'IncomingMail', 'DomicileLetter', 'PovertyLetter',
    'MarriageLetter', 'KtpLetter', 'LossLetter', 'IncomeLetter',
    'HeirLetter', 'AssistanceLetter'
];

const colorsToReplace = ['blue', 'indigo', 'rose', 'amber', 'purple', 'teal'];

directories.forEach(dir => {
    const file = path.join('resources/js/Pages', dir, 'Index.vue');
    if (!fs.existsSync(file)) return;
    
    let content = fs.readFileSync(file, 'utf8');

    colorsToReplace.forEach(color => {
        // Main Header text color
        content = content.replace(new RegExp(`text-${color}-600 dark:text-${color}-400`, 'g'), 'text-primary dark:text-primary');
        content = content.replace(new RegExp(`text-${color}-600`, 'g'), 'text-primary');
        
        // Button classes
        content = content.replace(
            new RegExp(`rounded-(xl|md) bg-${color}-600 px-4 py-2.5 text-xs font-bold text-white shadow-md shadow-${color}-600\\/20 transition hover:bg-${color}-500`, 'g'),
            `rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90`
        );
        content = content.replace(
            new RegExp(`rounded-(xl|md) bg-gradient-to-r from-${color}-600 to-[\\w]+-600 px-4 py-2\\.5 text-xs font-bold text-white shadow-md shadow-${color}-600\\/20 transition hover:from-${color}-500 hover:to-[\\w]+-500`, 'g'),
            `rounded-md bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-primary/90`
        );

        // Focus rings
        content = content.replace(new RegExp(`focus:border-${color}-500`, 'g'), 'focus:border-primary');
        content = content.replace(new RegExp(`focus:ring-${color}-500(\\/20)?`, 'g'), 'focus:ring-primary/20');
        
        // Pagination Active
        content = content.replace(new RegExp(`bg-${color}-600 text-white border-${color}-600`, 'g'), 'bg-primary text-white border-primary font-bold');
        content = content.replace(new RegExp(`hover:bg-${color}-50`, 'g'), 'hover:bg-primary/5');

        // Alert
        content = content.replace(new RegExp(`border-${color}-300 bg-${color}-50`, 'g'), 'border-primary/30 bg-primary/5');
        content = content.replace(new RegExp(`dark:border-${color}-700 dark:bg-${color}-950\\/60 dark:text-${color}-200`, 'g'), 'dark:border-primary/20 dark:bg-primary/10 dark:text-white');
        
        // Icons
        content = content.replace(new RegExp(`text-${color}-700`, 'g'), 'text-primary');
        
        // Pagination inactive
        content = content.replace(new RegExp(`hover:text-${color}-700`, 'g'), 'hover:text-primary');
        content = content.replace(new RegExp(`dark:text-${color}-400`, 'g'), 'dark:text-primary');
    });

    // Make sure we have the padding correct on table cells
    content = content.replace(/py-3 text-sm/g, 'py-4 text-sm');

    fs.writeFileSync(file, content);
});

console.log("Secondary color cleanup applied.");
