# [Sleek Core](https://github.com/powerbuoy/sleek-core/)

[![Packagist](https://img.shields.io/packagist/vpre/powerbuoy/sleek-core.svg?style=flat-square)](https://packagist.org/packages/powerbuoy/sleek-core)
[![GitHub license](https://img.shields.io/github/license/powerbuoy/sleek-core.svg?style=flat-square)](https://github.com/powerbuoy/sleek-core/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/powerbuoy/sleek-core.svg?style=flat-square)](https://github.com/powerbuoy/sleek-core/issues)
[![GitHub forks](https://img.shields.io/github/forks/powerbuoy/sleek-core.svg?style=flat-square)](https://github.com/powerbuoy/sleek-core/network)
[![GitHub stars](https://img.shields.io/github/stars/powerbuoy/sleek-core.svg?style=flat-square)](https://github.com/powerbuoy/sleek-core/stargazers)

Adds a bunch of theme support, includes translation files, enqueues scripts etc etc.

## Theme Support

### `sleek/disable_jquery`

Disable jQuery on the front end (not inside admin). Note that this may break some plug-ins.

### `sleek/jquery_cdn`

Include jQuery from a CDN (code.jquery.com).

### `sleek/get_terms_post_type_arg`

Adds support for a `post_type` argument to `get_terms` so it only returns terms associated with that post-type.

### `sleek/disable_theme_editor`

Disables the theme editor.

### `sleek/classic_editor`

Disables Gutenberg and enables the classic editor everywhere.

### `sleek/nice_email_from`

Changes the default email and name when using `wp_mail()` to use the site name and admin email instead of "WordPress".

### `sleek/disable_404_guessing`

Disables WordPress' insane guessing when it hits a 404: https://core.trac.wordpress.org/ticket/16557

## Hooks

### `sleek/jquery_version`

Return a jQuery version like "3.4.1" to change it.

### `sleek/meta_viewport`

Set a custom meta_viewport instead of the default `width=device-width, initial-scale=1.0`.

## Functions

N/A

## Classes

N/A
