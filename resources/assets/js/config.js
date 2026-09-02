/**
 * Config - NEXUS Theme Palette
 * -------------------------------------------------------------------------------------
 */

'use strict';

// JS global variables
window.config = {
  colors: {
    primary: '#37E780',
    secondary: '#959FAE',
    success: '#54DD7D',
    info: '#15BDFC',
    warning: '#F4BF3F',
    danger: '#F84A54',
    dark: '#1B222F',
    black: '#000',
    white: '#fff',
    cardColor: '#131823',
    bodyBg: '#090D16',
    bodyColor: '#F2F5F9',
    headingColor: '#F2F5F9',
    textMuted: '#959FAE',
    borderColor: '#232B3B'
  },
  colors_label: {
    primary: '#37E78029',
    secondary: '#959FAE29',
    success: '#54DD7D29',
    info: '#15BDFC29',
    warning: '#F4BF3F29',
    danger: '#F84A5429',
    dark: '#1B222F29'
  },
  colors_dark: {
    cardColor: '#131823',
    bodyBg: '#090D16',
    bodyColor: '#F2F5F9',
    headingColor: '#F2F5F9',
    textMuted: '#959FAE',
    borderColor: '#232B3B'
  },
  enableMenuLocalStorage: true
};

window.assetsPath = document.documentElement.getAttribute('data-assets-path');
window.baseUrl = document.documentElement.getAttribute('data-base-url') + '/';
window.templateName = document.documentElement.getAttribute('data-template');
window.rtlSupport = true;
