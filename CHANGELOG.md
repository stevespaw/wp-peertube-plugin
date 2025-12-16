# Changelog

All important changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/de/1.0.0/),
and this project follows [Semantic Versioning](https://semver.org/lang/de/).

## [1.0.0] - 2025-01-XX

### Added
- Initial release of the PeerTube Video Manager plugin
- 4 main shortcodes:
  - `[pt-last-videos]` - Shows latest videos of the instance
  - `[pt-latest-per-channel]` - Shows latest video per channel
  - `[pt-channel-videos]` - Shows videos from a specific channel
  - `[pt-video]` - Shows single video with details
- 2 search shortcodes:
  - `[pt-search]` - search form
  - `[pt-search-results]` - Search results with pagination
- Full Admin Settings Page:
  - PeerTube instance URL configuration
  - Standard channels management
  - Cache time settings
  - Connection test function
  - Cache clear function
- Intelligent caching system:
  - Video lists: 5 minutes (configurable)
  - Configuration: 24 hours (configurable)
  - Individual videos: 10 minutes
  - Search results: 2 minutes
- Custom plugin data support:
  - Send responsibility (from peertube-plugin-okas-dev)
  - Video number (from peertube-plugin-okas-dev)
- Video search by video number: `[pt-video number="12345"]`
- Full metadata display:
  - Video title and thumbnail
  - Duration/Length
  - Category (with mapping from PeerTube configuration)
  - Release date (relative: "1 day ago")
  - View count
  - Tags (up to 5 per card, all in detailed view)
  - Sending responsibility
  - Video number
  - Description (with HTML sanitization)
- Responsive Design:
  - Mobile: 1 column
  - Tablet: 2 columns
  - Desktop: 3-4 columns
  - CSS Grid for optimal layout
- Security features:
  - Input sanitization for all user inputs
  - Output escaping for all outputs
  - Nonces for AJAX requests
  - Capability checks for admin functions
  - wp_kses for secure HTML descriptions
- Performance optimizations:
  - Transient-based caching
  - Lazy loading for images
  - Respect PeerTube API rate limits (50 req/10 sec)
  - Efficient database queries
- German translation:
  - Fully translated
  - POT file for additional languages
  - German (de_DE) .po/.mo files
- Extensive documentation:
  - README.md with full description
  - USAGE_DE.md for end users
  - Inline code documentation
  - Shortcode examples in admin interface

### Technical details
- Minimum WordPress version: 6.0
- Minimum PHP version: 7.4
- PeerTube API v1 support
- Compatible with all modern browsers
- CORS compliant API requests
- Gutenberg compatible
- Classic Editor compatible

### API endpoints
- GET /api/v1/videos - List of all videos
- GET /api/v1/videos/{id} - Single video
- GET /api/v1/video-channels/{handle}/videos - Channel videos
- GET /api/v1/search/videos - Video search
- GET /api/v1/config - Instance configuration

### Known limitations
- Video search by number searches a maximum of 500 videos
- Federated videos may have restricted metadata
- Cache must be cleared manually for instant updates

## [1.1.6] - 2025-01-XX

### Added
- Integration of PeerTube search into the standard WordPress search form
- Dropdown selection for search area (WordPress website or PeerTube videos)
- JavaScript-based modification of the search form for better compatibility
- Dropdown options customizable text settings:
  - "Text for 'Search on website' option"
  - "Text for 'Search in PeerTube Videos' option"
- Automatic redirection to PeerTube search page when selecting "Search PeerTube videos"
- Support for both search parameters (`s` and `pt_search`) in shortcodes

### Fixed
- Improved compatibility with various WordPress themes
- CSS overrides for correct search form display
- Error handling in JavaScript code

### Changed
- Default values for dropdown options:
  - "Search on the website" (instead of "Search on the website")
  - “Search in the LokalMedial.de media library” (instead of “Search in PeerTube videos”)

## [1.1.5] - 2025-01-XX

### Fixed
- Improved logic for automatic video display on video page
- More reliable page checking (by ID and URL)
- Improved quality of video thumbnails (uses previewPath instead of thumbnailPath)
- Responsive images with srcset for better performance
- Improved mobile responsiveness for video detail page

### Added
- Support for `thumbnailUrl` from PeerTube API
- `get_thumbnail_srcset()` function for responsive images
- Improved CSS rules for image quality

## [1.1.4] - 2025-01-XX

### Fixed
- Improved pagination with modern design
- Round badges for page numbers
- Using theme colors for pagination

## [1.1.3] - 2025-01-XX

### Added
- Separate page for video viewing
- Automatic creation of the video page upon activation
- Setting for video page selection

### Fixed
- Video links now lead to dedicated WordPress site instead of PeerTube
- Auto display only works on configured video page

## [1.1.2] - 2025-01-XX

### Fixed
- "Please enter a search term." is only displayed on the first visit
- After searching, the message will be hidden

## [1.1.1] - 2025-01-XX

### Added
- Customizable button colors in settings
- WordPress Color Picker Integration
- CSS variables for button colors
- Customizable text for PeerTube button

## [1.1.0] - 2025-01-XX

### Added
- `columns` parameter for all video shortcodes
- Support for 1-6 columns or `auto`
- Automatic adaptation on mobile devices

## [1.0.9] - 2025-01-XX

### Added
- Automatic search page creation upon activation
- Setting for search page selection
- Clickable hashtags lead to search page

## [1.0.8] - 2025-01-XX

### Added
- SVG icons instead of emoji for metadata
- "Sending Responsibility:" Label before name
- Optional display of views (off by default)
- Improved CSS styles for icons

## [1.0.7] - 2025-01-XX

### Fixed
- Order of metadata in video cards adjusted
- Hashtags are now clickable and lead to search page

## [Unreleased]

### Planned for future releases
- Gutenberg blocks as an alternative to shortcodes
- Video upload integration (if permissions exist)
- Playlist support
- Live stream viewing
- Advanced filter options
- AJAX based infinite scroll pagination
- Video favorites for logged in users
- Comment integration
- Statistics dashboard in the admin area
- Multi-instance support

## Versioning scheme

- MAJOR Version: Incompatible API changes
- MINOR version: New functions (downwards compatible)
- PATCH version: bug fixes (backwards compatible)

[1.1.6]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.6
[1.1.5]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.5
[1.1.4]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.4
[1.1.3]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.3
[1.1.2]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.2
[1.1.1]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.1
[1.1.0]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.1.0
[1.0.9]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.0.9
[1.0.8]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.0.8
[1.0.7]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.0.7
[1.0.0]: https://github.com/yarkolife/wp-peertube-plugin/releases/tag/v1.0.0
[Unreleased]: https://github.com/yarkolife/wp-peertube-plugin/compare/v1.1.6...HEAD