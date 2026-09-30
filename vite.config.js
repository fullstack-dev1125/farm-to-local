import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Public pages load the design's own CSS/JS from public/assets/ directly.
// Tailwind is intentionally absent (CLAUDE.md). Vite stays only for future Alpine.js bundling.
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
});
