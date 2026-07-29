import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { expect, test } from '@playwright/test'

// package.json sets "type": "module", so this file runs as ESM — no CommonJS
// __dirname global — derive the equivalent from import.meta.url instead.
const __dirname = dirname(fileURLToPath(import.meta.url))

const SVG_PATH = resolve(__dirname, '../assets/star.svg')

test('renders the selected icon in the preview holder', async ({ page }) => {
  // Unique per run: nothing cleans these records up between runs, so fixed
  // titles would match records left by earlier runs and trip Playwright's
  // strict-mode locator check on the second execution.
  const runId = String(Date.now())
  const iconTitle = `Star ${runId}`
  const demoTitle = `Icon demo ${runId}`

  // 1. Create an Icon and upload the SVG.
  await page.goto('/admin/icons')
  await page.getByRole('link', { name: 'Add new Icon' }).click()

  await page.getByLabel('Title').fill(iconTitle)
  // The UploadField's own labelled input is a separate, inert element; the
  // Dropzone.js library (which actually drives the upload) listens on its own
  // hidden input instead, so setInputFiles must target that one.
  await page.getByLabel('dropzone hidden input').setInputFiles(SVG_PATH)
  // '.uploadfield-item' appears optimistically as soon as the file is picked,
  // before the async upload has actually attached it — clicking Create at
  // that point saves the record with no file. The "File uploaded" badge is
  // the real completion signal.
  await expect(page.getByLabel('File uploaded')).toBeVisible()

  await page.getByRole('button', { name: 'Create' }).click()
  // Two elements carry this text: a hidden status <p> (no "successfully"
  // suffix) and the visible toast (which has it) — match the toast's exact
  // phrasing so the assertion targets the one actually shown to the user.
  await expect(page.getByText(`Saved Icon "${iconTitle}" successfully.`)).toBeVisible()

  // 2. Create a demo object to host the field, landing straight on its form.
  await page.goto('/admin/icon-demo')
  await page.getByRole('link', { name: 'Add new Icon demo' }).click()
  await page.getByLabel('Title').fill(demoTitle)
  await page.getByRole('button', { name: 'Create' }).click()
  await expect(page.getByText(`Saved Icon demo "${demoTitle}" successfully.`)).toBeVisible()

  // 3. Selecting the icon fills the preview holder with its markup.
  const select = page.locator('select[data-icon-preview-endpoint]')
  await expect(select).toHaveAttribute('data-icon-preview-endpoint', /preview/)

  const holder = page.locator('.icon-preview-holder')
  await expect(holder).toHaveText('No icon selected')

  await select.selectOption({ label: iconTitle })

  await expect(holder.locator('[data-testid="star-icon"]')).toBeVisible()
})
