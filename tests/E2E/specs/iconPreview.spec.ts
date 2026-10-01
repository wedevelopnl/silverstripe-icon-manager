import { dirname, resolve } from 'node:path'
import { fileURLToPath } from 'node:url'
import { expect, type Locator, type Page, test } from '@playwright/test'

// package.json sets "type": "module", so this file runs as ESM — no CommonJS
// __dirname global — derive the equivalent from import.meta.url instead.
const __dirname = dirname(fileURLToPath(import.meta.url))

const SVG_PATH = resolve(__dirname, '../assets/star.svg')
const PNG_PATH = resolve(__dirname, '../assets/star.png')

async function createIcon(page: Page, title: string, filePath: string): Promise<void> {
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

/**
 * Create a demo object to host the field, select `iconTitle` in it and return
 * the preview holder.
 */
async function selectIconInDemo(
  page: Page,
  demoTitle: string,
  iconTitle: string,
): Promise<Locator> {
  await page.goto('/admin/icon-demo')
  await page.getByRole('link', { name: 'Add new Icon demo' }).click()
  await page.getByLabel('Title').fill(demoTitle)
  await page.getByRole('button', { name: 'Create' }).click()
  await expect(page.getByText(`Saved Icon demo "${demoTitle}" successfully.`)).toBeVisible()

  const select = page.locator('select[data-icon-preview-endpoint]')
  await expect(select).toHaveAttribute('data-icon-preview-endpoint', /preview/)

  const holder = page.locator('.icon-preview-holder')
  await expect(holder).toHaveText('No icon selected')

  await select.selectOption({ label: iconTitle })

  return holder
}

// Unique per run: nothing cleans these records up between runs, so fixed
// titles would match records left by earlier runs and trip Playwright's
// strict-mode locator check on the second execution.
const runId = String(Date.now())

test('renders a selected SVG icon inline in the preview holder', async ({ page }) => {
  const iconTitle = `Star SVG ${runId}`
  await createIcon(page, iconTitle, SVG_PATH)

  const holder = await selectIconInDemo(page, `Icon demo SVG ${runId}`, iconTitle)

  await expect(holder.locator('[data-testid="star-icon"]')).toBeVisible()
})

// The testbed adds png to the `wedevelop/icon` category exactly as
// docs/configuration.md tells consumers to (.docker/app/_config).
test('renders a selected PNG icon as a loaded image in the preview holder', async ({ page }) => {
  const iconTitle = `Star PNG ${runId}`
  await createIcon(page, iconTitle, PNG_PATH)

  const holder = await selectIconInDemo(page, `Icon demo PNG ${runId}`, iconTitle)

  // An <img> rules out raw file bytes dumped into the holder; its natural
  // width (star.png is 24px wide) proves the browser could actually fetch the
  // draft asset, which a broken URL or missing access grant would not allow.
  const image = holder.locator('img')
  await expect(image).toBeVisible()
  await expect(image).toHaveJSProperty('naturalWidth', 24)
})
