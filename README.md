# Moodle Theme Aurora

Aurora is the Whiteboard-style Boost child theme used by Aprende in the Industria Elearning IOMAD demo.

## Requirements

- Moodle 4.5+
- Boost parent theme

## Installation

Copy this repository into Moodle as `theme/aurora`, then run the Moodle upgrade and purge theme caches.

For Industria Elearning production, the theme is vendored into the `iomad-demo` image through the GitOps deployment flow. Do not upload or modify it inside a running pod.

## Assets

The `pix/whiteboard/login-title.png` asset is derived from the approved login composition at `clients/iomad-demo/.impeccable/mocks/decision/login-split-board.webp` in the deployment repository.

## License

GPL-3.0-or-later.
