# PeerTube Video Manager

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)

A WordPress plugin to integrate PeerTube videos with shortcodes. Displays videos, channels, search and custom metadata including send responsibility.

**Repository:** [https://github.com/yarkolife/wp-peertube-plugin](https://github.com/yarkolife/wp-peertube-plugin)

## Features

- **4 shortcodes** for different video views
- **Intelligent caching** for optimal performance
- **Responsive Design** for all screen sizes
- **Custom metadata** from PeerTube plugins
- **Search** with pagination
- **Configurable backend** with settings page
- **German translation** included

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- PeerTube instance (API access)

## Installation

### About WordPress Admin

1. Download the [ZIP file](https://github.com/yarkolife/wp-peertube-plugin/releases/latest).
2. Go to `Plugins > Install > Upload Plugin`
3. Select the ZIP file
4. Click “Install Now”
5. Activate the plugin

### About Git (for developers)

```bash
cd wp-content/plugins/
git clone https://github.com/yarkolife/wp-peertube-plugin.git peertube-video-manager
```

### Manually

1. Upload the plugin files to `/wp-content/plugins/peertube-video-manager/`
2. Activate the plugin from the 'Plugins' menu in WordPress

## Configuration

Once activated, go to `Settings > PeerTube Videos`:

- **PeerTube instance URL**: The URL of your PeerTube instance (e.g. `https://lokalmedial.de`)
- **Standard Channels**: List of channel handles (one per line)
- **Cache Times**: How long data is cached
- **Videos per page**: Default number of videos displayed

## Shortcodes

### [pt-last-videos]

Displays the latest videos of the instance.

**Attributes:**
- `count` (optional, default: 8) - number of videos
- `host_only` (optional, default: "true") - Local videos only

**Examples:**
```
[pt-last-videos]
[pt-last-videos count="12"]
[pt-last-videos count="6" host_only="false"]
```

### [pt-latest-per-channel]

Displays the latest video from each channel.

**Attributes:**
- `channels` (optional) - Comma separated list of channel handles

**Examples:**
```
[pt-latest-per-channel]
[pt-latest-per-channel channels="ok_dessau,ok_magdeburg,okmq"]
```

If no `channels` attribute is specified, the plugin will use the default channels from the settings.

### [pt-channel-videos]

Shows videos from a specific channel.

**Attributes:**
- `channel` (required) - Channel handle
- `count` (optional, default: 6) - number of videos

**Examples:**
```
[pt-channel-videos channel="okmq"]
[pt-channel-videos channel="ok_dessau" count="10"]
```

### [pt video]

Displays a single video with all details.

**Attributes:**
- `id` - Video UUID or shortUUID
- `number` - video number (from plugin data)

**Examples:**
```
[pt-video id="xc86cB87iZXsgCofjHVcYJ"]
[pt-video number="12345"]
```

**Note:** Either `id` or `number` must be specified.

### [pt-search]

Displays a search form.

**Attributes:**
- `placeholder` (optional) - placeholder text
- `action` (optional) - Destination URL for search results

**Examples:**
```
[pt-search]
[pt-search placeholder="Search videos..."]
[pt-search action="/search results/"]
```

### [pt-search-results]

Displays search results with pagination.

**Attributes:**
- `per_page` (optional, default: 12) - Videos per page

**Examples:**
```
[pt-search-results]
[pt-search-results per_page="20"]
```

## Metadata displayed

The following information is displayed for each video:

- **Thumbnail** - Video thumbnail
- **Title** - Video name
- **Length** - Duration of the video (⏱)
- **Category** - Video Category (🏷)
- **Publication Date** - Relative Date (📅)
- **Views** - Number of views (👁)
- **Sending Responsibility** - From PeerTube plugin (👤)
- **Video Number** - From PeerTube plugin (🔢)
- **Tags** - Up to 5 tags per video

## Caching

The plugin uses WordPress Transients for caching:

- **Video Lists**: 5 minutes (configurable)
- **Configuration**: 24 hours (configurable)
- **Individual Videos**: 10 minutes
- **Search results**: 2 minutes

To clear the cache, go to Settings > PeerTube Videos and click Clear Cache.

## Performance

- Respects PeerTube API rate limits (50 requests/10 seconds)
- Intelligent caching reduces API calls
- Lazy loading for images
- Responsive CSS grid for optimal display

## Customizations

### Customize CSS

You can override the styles by adding your own CSS rules in your theme:

```css
/* Example: Adjust video cards */
.pt-video-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
```

### Overwrite templates

Copy the template files from `templates/` into your theme directory:

```
your-theme/peertube-video-manager/video-card.php
your-theme/peertube-video-manager/video-detail.php
your-theme/peertube-video-manager/search-form.php
```

## Common problems

### No videos are displayed

1. Check the PeerTube URL in Settings
2. Click on “Test connection”
3. Clear the cache
4. Check the browser console for errors

### Videos are not updated

Clear the cache via `Settings > PeerTube Videos > Clear Cache`.

### 404 errors on video URLs

Make sure the PeerTube URL is correct and the videos are publicly accessible.

### Slow loading times

- Reduce the number of videos per page
- Increase cache time
- Check the connection to the PeerTube instance

## Development

### Structure

```
peertube video manager/
├── peertube-video-manager.php # Main file
├── includes/ # Core classes
├── shortcodes/ # Shortcode classes
├── templates/ # Template files
├── assets/ # CSS & JS
└── languages/ # Translations
```

### Hooks & Filters

The plugin offers various hooks for developers:

```php
// Filter video data before rendering
add_filter('pt_vm_video_data', function($video) {
    // Modify video data
    return $video;
});

// Action after clearing the cache
add_action('pt_vm_cache_cleared', function() {
    // Do something
});
```

## Changelog

### Version 1.0.0
- First release
- 4 shortcodes implemented
- Caching system
- Admin settings page
- German translation

## Support

If you have any questions or problems:

1. Check the [Documentation](docs/USAGE_DE.md)
2. Enable WP_DEBUG for detailed errors
3. Create an [Issue on GitHub](https://github.com/yarkolife/wp-peertube-plugin/issues)

## Contribute

Contributions are welcome! Please:

1. Fork the [repository](https://github.com/yarkolife/wp-peertube-plugin)
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a [Pull Request](https://github.com/yarkolife/wp-peertube-plugin/pulls)

## License

GPL v2 or higher

## Credits

Designed to integrate PeerTube videos into WordPress. Supports custom plugin data from `peertube-plugin-okas-dev`.

## Technical details

### API endpoints

The plugin uses the following PeerTube API endpoints:

- `GET /api/v1/videos` - List of all videos
- `GET /api/v1/videos/{id}` - Single video
- `GET /api/v1/video-channels/{handle}/videos` - Channel videos
- `GET /api/v1/search/videos` - Video search
- `GET /api/v1/config` - Instance configuration

### Security

- All user input is sanitized
- Expenses are escaped
- Nonces for AJAX requests
- Capability checks for admin functions
- CORS compliant API requests

### Browser compatibility

- Chrome/Edge (Chromium) ✓
- Firefox ✓
- Safari ✓
- Mobile browsers ✓

## Contribute

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a pull request

## Author

Developed with ❤️ for the PeerTube community