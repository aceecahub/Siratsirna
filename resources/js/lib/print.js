function waitForImages() {
    const images = [...document.querySelectorAll('.print-sheet img')];

    return Promise.all(
        images.map(
            (img) =>
                new Promise((resolve) => {
                    // complete = sudah selesai (berhasil maupun gagal) -> lanjut.
                    if (img.complete) {
                        resolve();

                        return;
                    }

                    img.addEventListener('load', () => resolve(), {
                        once: true,
                    });
                    img.addEventListener('error', () => resolve(), {
                        once: true,
                    });
                }),
        ),
    );
}

export async function printSheet() {
    await Promise.all([
        waitForImages(),
        document.fonts?.ready ?? Promise.resolve(),
    ]);

    // Beri waktu browser menata ulang layout (reset transform skala ke ukuran asli).
    await new Promise((resolve) => setTimeout(resolve, 150));

    window.print();
}
