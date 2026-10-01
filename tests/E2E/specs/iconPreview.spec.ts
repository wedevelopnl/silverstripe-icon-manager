import { expect, type Locator, type Page, test } from '@playwright/test'
import { createIcon, PNG_PATH, SVG_PATH } from '../support/icons'

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
  // width proves the browser could actually fetch the asset, which a broken
  // URL would not allow.
  const image = holder.locator('img')
  await expect(image).toBeVisible()
  await expect(image).toHaveJSProperty('naturalWidth', 24)
})
