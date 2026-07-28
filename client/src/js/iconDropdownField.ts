// Keyed on the data attribute alone, not on `.icondropdown`. That class comes from
// FormField::Type() stripping the `Field` suffix off the class name, so it would break
// silently if the PHP class were ever renamed. The data attribute is set explicitly by
// IconDropdownField::getAttributes() and is unique to this field.
const SELECTOR = 'select[data-icon-preview-endpoint]'
const BOUND_FLAG = 'iconPreviewBound'

const LOADING_MESSAGE = 'Loading preview…'
const EMPTY_MESSAGE = 'No icon selected'
const ERROR_MESSAGE = 'Could not load the icon preview'

// Keyed by `${endpoint}?icon=${id}` so two fields pointing at different
// endpoints never share a cached response. Deliberately module-scoped (not
// tied to the <select> element): the CMS keeps this module loaded for the
// whole Pjax session while tearing down and recreating the DOM on every
// navigation, so a cache keyed on DOM-node identity would lose every hit as
// soon as a field's form re-rendered — exactly the case this cache exists for.
const cache = new Map<string, string>()

function holderFor(select: HTMLSelectElement): HTMLElement | null {
  return document.getElementById(`${select.id}_preview`)
}

async function fetchPreview(url: string): Promise<string> {
  const cached = cache.get(url)
  if (cached !== undefined) {
    return cached
  }

  const response = await fetch(url, { credentials: 'same-origin' })
  if (!response.ok) {
    throw new Error(`Preview request failed with status ${response.status}`)
  }

  const markup = await response.text()
  cache.set(url, markup)

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
    holder.innerHTML = await fetchPreview(`${endpoint}?icon=${encodeURIComponent(select.value)}`)
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
