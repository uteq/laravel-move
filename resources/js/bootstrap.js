// Flatpickr Calendar
const flatpickr = require("flatpickr").default;
const Dutch = require('flatpickr/dist/l10n/nl.js').default.nl;
flatpickr.localize(Dutch);
window.flatpickr = flatpickr;

// IMask to add input masks support
import IMask from 'imask';
window.IMask = IMask;

// PopperJS for the best element alignment
import { createPopper } from '@popperjs/core/lib/popper-lite.js';
import preventOverflow from '@popperjs/core/lib/modifiers/preventOverflow.js';
import flip from '@popperjs/core/lib/modifiers/flip.js';
window.createPopper = createPopper;
window.preventOverflow = preventOverflow;
window.flip = flip;

import * as $ from 'jquery';
window.$ = $;

import select2 from './select2';
window.loadSelect2 = select2;

$('.select2').select2();

import moveSearch from './move-search';
window.moveSearch = moveSearch;

import Quill from 'quill';
window.Quill = Quill;
