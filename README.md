# Feed Embedder for Substack

Embed the latest posts from any public [Substack](https://substack.com) newsletter anywhere on your WordPress site with a simple, fully customizable shortcode.

**WordPress.org:** https://wordpress.org/plugins/feed-embedder-for-substack/

```
[fefs_feed]
```

## Features

- **Simple shortcode** — drop `[fefs_feed]` into any post, page or widget.
- **Per-instance overrides** — `[fefs_feed url="https://other.substack.com" count="4" layout="vertical"]`.
- **Two layouts** — horizontal cards (image beside text) or a vertical grid of 1–3 columns.
- **Full design control** — colors, typography, spacing, borders, hover effects, mobile options.
- **Live preview** in the admin, before saving.
- **Smart caching** with WordPress transients (including negative caching for failed feeds).
- **Lightweight & privacy-friendly** — no external scripts, no iframes, no tracking; only the public RSS feed is fetched, server-side.

## Usage

1. Go to **Settings → Feed for Substack**.
2. Enter your Substack URL (e.g. `https://example.substack.com`) and choose how many posts to show.
3. Style the cards in the **Design** tab and check the **Preview** tab.
4. Paste `[fefs_feed]` anywhere shortcodes are supported.

### Shortcode attributes

| Attribute | Description |
| --------- | ----------- |
| `url`     | Override the Substack URL for that instance. |
| `count`   | Override the number of posts (1–20). |
| `layout`  | `horizontal` or `vertical`. |

## Development

- Requires WordPress 6.0+ and PHP 7.4+. No build step, no dependencies.
- User-facing strings are internationalized (text domain `feed-embedder-for-substack`).
- Releases are published to WordPress.org automatically by pushing a version tag (see `.github/workflows/deploy.yml`).

## Contributing

Issues and pull requests are welcome. Please keep changes focused and follow the WordPress coding standards.

## Disclaimer

This is an independent, third-party plugin and is not affiliated with, endorsed by, or sponsored by Substack Inc. "Substack" is a trademark of Substack Inc.

## License

[GPL-2.0-or-later](LICENSE).
