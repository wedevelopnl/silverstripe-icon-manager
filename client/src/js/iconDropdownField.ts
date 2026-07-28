// Keyed on the data attribute alone, not on `.icondropdown`. That class comes from
// FormField::Type() stripping the `Field` suffix off the class name, so it would break
// silently if the PHP class were ever renamed. The data attribute is set explicitly by
// IconDropdownField::getAttributes() and is unique to this field.
const SELECTOR = 'select[data-icon-preview-endpoint]'
const BOUND_FLAG = 'iconPreviewBound'

const LOADING_MESSAGE = 'Loading preview…'
const EMPTY_MESSAGE = 'No icon selected'
const ERROR_MESSAGE = 'Could not load the icon preview'

// Cached per select, then keyed by `${endpoint}?icon=${id}` within that field's
// own map. Scoping by field (rather than one flat, module-wide map keyed on the
// URL alone) means two fields can never bleed cached responses into each other
// even if their endpoints were ever to collide.
const cache = new WeakMap<HTMLSelectElement, Map<string, string>>()

function holderFor(select: HTMLSelectElement): HTMLElement | null {
  return document.getElementById(`${select.id}_preview`)
}

async function fetchPreview(select: HTMLSelectElement, url: string): Promise<string> {
  let selectCache = cache.get(select)
  if (selectCache === undefined) {
    selectCache = new Map<string, string>()
    cache.set(select, selectCache)
  }

  const cached = selectCache.get(url)
  if (cached !== undefined) {
    return cached
  }

  const response = await fetch(url, { credentials: 'same-origin' })
  if (!response.ok) {
    throw new Error(`Preview request failed with status ${response.status}`)
  }

  const markup = await response.text()
  selectCache.set(url, markup)

  return markup
}

async function updatePreview(select: HTMLSelectElement): Promise<void> {
  const holder = holderFor(select)
  if (holder === null) {
    return
  }

  const endpoint = select.dataset.iconPreviewEndpoint
  if (endpoint === undefined || endpoint === '') {
    return
  }

  if (select.value === '') {
    holder.innerHTML = EMPTY_MESSAGE
    return
  }

  holder.innerHTML = LOADING_MESSAGE

  try {
    holder.innerHTML = await fetchPreview(
      select,
      `${endpoint}?icon=${encodeURIComponent(select.value)}`,
    )
  } catch {
    holder.innerHTML = ERROR_MESSAGE
  }
}

/**
 * Bind every unbound icon dropdown within `root`. Safe to call repeatedly —
 * fields already bound are skipped via a dataset flag.
 */
export function initIconDropdowns(root: ParentNode): void {
  for (const select of root.querySelectorAll<HTMLSelectElement>(SELECTOR)) {
    if (select.dataset[BOUND_FLAG] === 'true') {
      continue
    }

    select.dataset[BOUND_FLAG] = 'true'
    select.addEventListener('change', (event) => {
      // The CMS binds its own change handlers higher up the tree; stop the
      // event so selecting an icon does not trigger unrelated form behaviour.
      event.stopPropagation()
      void updatePreview(select)
    })
  }
}

/**
 * Watch `target` for icon dropdowns added after load.
 *
 * The CMS loads ModelAdmin and page forms over Pjax, so fields routinely appear
 * long after DOMContentLoaded. jQuery.entwine's `onmatch` is unreliable for that
 * content, so this uses a plain MutationObserver.
 */
export function observeIconDropdowns(target: Node): MutationObserver {
  const observer = new MutationObserver(() => {
    initIconDropdowns(document)
  })

  observer.observe(target, { childList: true, subtree: true })

  return observer
}
