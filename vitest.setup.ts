import { afterEach, vi } from 'vitest'
import failOnConsole from 'vitest-fail-on-console'

// Fail any test that logs an unexpected console.error/warn.
failOnConsole({
  shouldFailOnError: true,
  shouldFailOnWarn: true,
})

afterEach(() => {
  vi.restoreAllMocks()

  if (vi.isMockFunction(globalThis.fetch)) {
    vi.mocked(globalThis.fetch).mockRestore()
  }
})
