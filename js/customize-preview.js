/**
 * Customizer live preview
 */
(function ($) {
  'use strict';

  var colorMappings = {
    knt_color_accent: '--color-accent',
    knt_color_accent_light: '--color-accent-light',
    knt_color_bg_main: '--color-bg-main',
    knt_color_bg_secondary: '--color-bg-secondary',
    knt_color_text_main: '--color-text-main',
  };

  Object.keys(colorMappings).forEach(function (setting) {
    wp.customize(setting, function (value) {
      value.bind(function (to) {
        document.documentElement.style.setProperty(colorMappings[setting], to);
      });
    });
  });
})(jQuery);
