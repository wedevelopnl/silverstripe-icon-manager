/// <reference types="vitest/config" />

import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { defineConfig } from 'vite'

const __dirname = dirname(fileURLToPath(import.meta.url))

export default defineConfig({
  resolve: {
    alias: {
      '@': resolve(__dirname, 'client/src/js'),
    },
  },
  build: {
    outDir: 'client/dist',
    // SilverStripe's vendor-plugin symlinks public/_resources/ back to
    // client/dist — copying a public dir would recurse infinitely.
    copyPublicDir: false,
    lib: {
      entry: resolve(__dirname, 'client/src/js/bundle.ts'),
      name: 'IconManager',
      formats: ['iife'],
      fileName: () => 'js/bundle.js',
    },
    rolldownOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.names.some((name) => name.endsWith('.css'))) {
            return 'styles/bundle.css'
          }
          return 'assets/[name][extname]'
        },
      },
    },
  },
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['client/src/js/**/*.{test,spec}.ts'],
    setupFiles: ['./vitest.setup.ts'],
    coverage: {
      include: ['client/src/js/**/*.ts'],
      exclude: ['client/src/js/bundle.ts', 'client/src/js/**/*.d.ts'],
      thresholds: {
        statements: 90,
        branches: 85,
        functions: 90,
        lines: 90,
      },
    },
  },
})
