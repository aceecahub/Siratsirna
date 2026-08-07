const fs = require('fs');
let content = fs.readFileSync('resources/js/Layouts/AuthenticatedLayout.vue', 'utf8');

// Replace active styles
content = content.replace(/bg-emerald-50 text-emerald-700 font-bold dark:bg-emerald-950\/60 dark:text-emerald-300 ring-1 ring-emerald-500\/20/g, "bg-primary/10 text-primary font-bold dark:bg-primary/20 dark:text-white border-r-2 border-primary");

// Replace rounded-xl with rounded-md
content = content.replace(/rounded-xl/g, 'rounded-md');

// Replace general background
content = content.replace(/bg-slate-100\/60/g, 'bg-canvas');

// Sidebar border
content = content.replace(/border-r border-slate-200\/80 dark:border-slate-800\/80 shadow-xl lg:shadow-none/g, "border-r border-slate-200 dark:border-slate-800 shadow-sm lg:shadow-none");

// Drop gradient logo background
content = content.replace(/bg-gradient-to-tr from-emerald-600 to-teal-500/g, "bg-primary");
content = content.replace(/shadow-md shadow-emerald-500\/30 ring-1 ring-emerald-500\/30/g, "");

// Top logo border gradient line
content = content.replace(/<div class="h-1 w-full bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-600 shrink-0" \/>/g, '<div class="h-1 w-full bg-primary shrink-0" />');

// E-Gov Surat text
content = content.replace(/text-emerald-600 dark:text-emerald-400/g, "text-primary dark:text-primary");

// Accordion trigger active background
content = content.replace(/bg-indigo-50 text-indigo-700 dark:bg-indigo-950\/50 dark:text-indigo-300/g, "bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white");

fs.writeFileSync('resources/js/Layouts/AuthenticatedLayout.vue', content);
