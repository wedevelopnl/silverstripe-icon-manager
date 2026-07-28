import { beforeEach, describe, expect, it, vi } from 'vitest'

const ENDPOINT = '/admin/icons/field/IconID/preview'

// The module keeps its fetch cache for its own lifetime by design (see the
// comment on `cache` in iconDropdownField.ts) — that's what lets a field
// survive a Pjax re-render without refetching. Re-importing the module fresh
// per test, rather than importing it once at the top of this file, gives each
// test its own cache instead of leaking entries between test cases that reuse
// the same endpoint and icon ids.
let initIconDropdowns: typeof import('./iconDropdownField').initIconDropdowns
let observeIconDropdowns: typeof import('./iconDropdownField').observeIconDropdowns

function renderField(id: string): HTMLSelectElement {
  const holder = document.createElement('div')
  holder.innerHTML = `
    <select id="${id}" class="icondropdown" data-icon-preview-endpoint="${ENDPOINT}">
      <option value=""></option>
      <option value="7">Star</option>
      <option value="9">Heart</option>
    </select>
    <div class="icon-preview-holder" id="${id}_preview">No icon selected</div>
  `
  document.body.append(holder)

  const select = document.getElementById(id)
  if (!(select instanceof HTMLSelectElement)) {
    throw new Error('select not rendered')
  }
  return select
}

function preview(id: string): string {
  return document.getElementById(`${id}_preview`)?.innerHTML.trim() ?? ''
}

function mockFetch(body: string): ReturnType<typeof vi.fn> {
  const fetchMock = vi.fn(() => Promise.resolve(new Response(body)))
  vi.stubGlobal('fetch', fetchMock)
  return fetchMock
}

async function change(select: HTMLSelectElement, value: string): Promise<void> {
  select.value = value
  select.dispatchEvent(new Event('change', { bubbles: true }))
  await vi.waitFor(() => {
    expect(preview(select.id)).not.toBe('Loading preview…')
  })
}

describe('iconDropdownField', () => {
  beforeEach(async () => {
    document.body.innerHTML = ''
    vi.resetModules()
    ;({ initIconDropdowns, observeIconDropdowns } = await import('./iconDropdownField'))
  })

  it('renders the fetched preview into its own holder', async () => {
    const select = renderField('Form_Field_IconID')
    mockFetch('<svg id="star"></svg>')
    initIconDropdowns(document)

    await change(select, '7')

    expect(preview('Form_Field_IconID')).toBe('<svg id="star"></svg>')
  })

  it('only updates the holder belonging to the changed field', async () => {
    const first = renderField('Form_Field_IconA')
    renderField('Form_Field_IconB')
    mockFetch('<svg id="star"></svg>')
    initIconDropdowns(document)

    await change(first, '7')

    expect(preview('Form_Field_IconA')).toBe('<svg id="star"></svg>')
    expect(preview('Form_Field_IconB')).toBe('No icon selected')
  })

  it('requests the endpoint with the selected icon id', async () => {
    const select = renderField('Form_Field_IconID')
    const fetchMock = mockFetch('<svg></svg>')
    initIconDropdowns(document)

    await change(select, '9')

    expect(fetchMock).toHaveBeenCalledWith(`${ENDPOINT}?icon=9`, expect.anything())
  })

  it('memoises a previously fetched icon', async () => {
    const select = renderField('Form_Field_IconID')
    const fetchMock = mockFetch('<svg></svg>')
    initIconDropdowns(document)

    await change(select, '7')
    await change(select, '9')
    await change(select, '7')

    expect(fetchMock).toHaveBeenCalledTimes(2)
  })

  it('clears the preview when the empty option is selected without fetching', async () => {
    const select = renderField('Form_Field_IconID')
    const fetchMock = mockFetch('<svg></svg>')
    initIconDropdowns(document)

    await change(select, '')

    expect(fetchMock).not.toHaveBeenCalled()
    expect(preview('Form_Field_IconID')).toBe('No icon selected')
  })

  it('shows an error message when the request fails', async () => {
    const select = renderField('Form_Field_IconID')
    vi.stubGlobal(
      'fetch',
      vi.fn(() => Promise.resolve(new Response('nope', { status: 500 }))),
    )
    initIconDropdowns(document)

    await change(select, '7')

    expect(preview('Form_Field_IconID')).toBe('Could not load the icon preview')
  })

  it('binds fields inserted after initialisation', async () => {
    mockFetch('<svg id="star"></svg>')
    const observer = observeIconDropdowns(document.body)

    const select = renderField('Form_Field_Late')
    await vi.waitFor(() => {
      expect(select.dataset.iconPreviewBound).toBe('true')
    })
    await change(select, '7')

    expect(preview('Form_Field_Late')).toBe('<svg id="star"></svg>')
    observer.disconnect()
  })

  it('does not bind the same field twice', () => {
    const select = renderField('Form_Field_IconID')
    const listener = vi.spyOn(select, 'addEventListener')

    initIconDropdowns(document)
    initIconDropdowns(document)

    expect(listener).toHaveBeenCalledTimes(1)
  })
})
