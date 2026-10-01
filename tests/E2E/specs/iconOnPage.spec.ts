import { type Browser, expect, type Locator, type Page, test } from '@playwright/test'
import { createIcon, PNG_PATH, SVG_PATH } from '../support/icons'

// The testbed Page (.docker/app) follows the docs/configuration.md example: a
// has_one to Icon, an IconDropdownField and a template rendering
// `$Icon.Icon.Tag` inside `.page-icon`.

/**
 * Create a page through the CMS, attach `iconTitle` to it, publish it and
 * return its path.
 */
async function publishPageWithIcon(page: Page, title: string, iconTitle: string): Promise<string> {
  await page.goto('/admin/pages/add')
  await page.getByRole('button', { name: 'Create' }).click()

  await page.getByLabel('Page name').fill(title)
  await page.locator('select[data-icon-preview-endpoint]').selectOption({ label: iconTitle })

  await page.getByRole('button', { name: 'Publish' }).click()
  await expect(page.getByRole('button', { name: 'Published' })).toBeVisible()

  // Read back, not derived from the title: the CMS only regenerates the
  // segment from the title while it still equals the bare `new-page` default,
  // so on any testbed that already holds a page it keeps `new-page-N`.
  return `/${await page.locator('input[name="URLSegment"]').inputValue()}`
}

/**
 * Open the published page as a logged-out visitor and return the icon wrapper.
 * A fresh context carries no CMS session, so it sees only what the live site
 * serves — no draft content and no session-granted access to protected assets.
 */
async function visitAsVisitor(browser: Browser, path: string): Promise<Locator> {
  const { baseURL } = test.info().project.use
  const context = await browser.newContext({ baseURL, ignoreHTTPSErrors: true })
  const visitor = await context.newPage()

  const response = await visitor.goto(path)
  expect(response?.status()).toBe(200)

  return visitor.locator('.page-icon')
}

// Unique per run: nothing cleans these records up between runs.
const runId = String(Date.now())

test('a published page renders its SVG icon inline for visitors', async ({ page, browser }) => {
  const iconTitle = `Star SVG ${runId}`
  await createIcon(page, iconTitle, SVG_PATH)
  const path = await publishPageWithIcon(page, `Icon page SVG ${runId}`, iconTitle)

  const icon = await visitAsVisitor(browser, path)

  await expect(icon.locator('[data-testid="star-icon"]')).toBeVisible()
})

test('a published page renders its PNG icon as a loaded image for visitors', async ({
  page,
  browser,
}) => {
  const iconTitle = `Star PNG ${runId}`
  await createIcon(page, iconTitle, PNG_PATH)
  const path = await publishPageWithIcon(page, `Icon page PNG ${runId}`, iconTitle)

  const icon = await visitAsVisitor(browser, path)

  // A visitor only gets the image bytes if the icon file was published along
  // with the page; an unpublished asset 404s and leaves naturalWidth at 0.
  const image = icon.locator('img')
  await expect(image).toBeVisible()
  await expect(image).toHaveJSProperty('naturalWidth', 24)
})
