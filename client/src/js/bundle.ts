import '../styles/main.scss'
import { initIconDropdowns, observeIconDropdowns } from './iconDropdownField'

initIconDropdowns(document)
// document.body is null when Requirements::set_write_javascript_to_body(false)
// puts this bundle in <head>; observing the root element still catches the body
// and everything the CMS renders into it.
observeIconDropdowns(document.body ?? document.documentElement)
