import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { expect, type Page } from '@playwright/test'

// package.json sets "type": "module", so this file runs as ESM — no CommonJS
// __dirname global — derive the equivalent from import.meta.url instead.
const __dirname = dirname(fileURLToPath(import.meta.url))

export const SVG_PATH = resolve(__dirname, '../assets/star.svg')
// 24px wide; specs assert that width to prove the browser loaded the image.
export const PNG_PATH = resolve(__dirname, '../assets/star.png')

export async function createIcon(page: Page, title: string, filePath: string): Promise<void> {
  await page.goto('/admin/icons')
  await page.getByRole('link', { name: 'Add new Icon' }).click()

  await page.getByLabel('Title').fill(title)
  // The UploadField's own labelled input is a separate, inert element; the
  // Dropzone.js library (which actually drives the upload) listens on its own
  // hidden input instead, so setInputFiles must target that one.
  await page.getByLabel('dropzone hidden input').setInputFiles(filePath)
  // '.uploadfield-item' appears optimistically as soon as the file is picked,
  // before the async upload has actually attached it — clicking Create at
  // that point saves the record with no file. The "File uploaded" badge is
  // the real completion signal.
  await expect(page.getByLabel('File uploaded')).toBeVisible()

  await page.getByRole('button', { name: 'Create' }).click()
  // Two elements carry this text: a hidden status <p> (no "successfully"
  // suffix) and the visible toast (which has it) — match the toast's exact
  // phrasing so the assertion targets the one actually shown to the user.
  await expect(page.getByText(`Saved Icon "${title}" successfully.`)).toBeVisible()
}
