const MONTHS = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

export function formatTanggal(value) {
    if (!value) return '-';

    const match = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);

    if (match) {
        const [, year, month, day] = match;

        return `${Number(day)} ${MONTHS[Number(month) - 1]} ${year}`;
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return `${date.getDate()} ${MONTHS[date.getMonth()]} ${date.getFullYear()}`;
}

export function formatBytes(bytes) {
    if (!bytes) return '0 MB';

    return `${(bytes / 1024 / 1024).toFixed(2)} MB`;
}
