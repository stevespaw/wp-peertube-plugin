# PeerTube Video Manager - User Guide

## Table of Contents

1. [facility](#facility)
2. [Shortcodes explained](#shortcodes-explained)
3. [Common Problems](#common-problems)
4. [Performance Optimization Tips](#performance-optimization-tips)

## Facility

### Initial installation

1. **Install and activate plugin**
   - Download the plugin zip file
   - Go to `Plugins > Install > Upload Plugin`
   - Select the ZIP file and click "Install Now"
   - Activate the plugin after installation

2. **Configure basic settings**
   - Go to `Settings > PeerTube Videos`
   - Enter the URL of your PeerTube instance (e.g. `https://video3.cappital.co`)
   - Click on "Test connection" to check the connection
   - Save settings

3. **Optional: Set up standard channels**
   - Enter channel handles in the "Standard Channels" text box
   - One channel handle per line (e.g. `ok_dessau`, `ok_magdeburg`)
   - These are used for `[pt-latest-per-channel]` when no channels are specified

### Configuration options

#### PeerTube instance URL
The full URL of your PeerTube instance, without a trailing slash.

**Example:** `https://video3.cappital.co`

#### Standard channels
List of channel handles used by default. Enter each channel on a new line.

**Example:**
```
ok_dessau
ok_magdeburg
okmq
```

#### Cache time for videos
How long video lists stay in the cache (in minutes). Default: 5 minutes.

**Recommendation:** 
- 5 minutes for frequently updated content
- 15-30 minutes for more static content

#### Configuration cache time
How long categories and configuration are cached (in hours). Default: 24 hours.

#### Videos per page
Default number of videos displayed without explicit indication. Default: 8.

## Shortcodes explained

### [pt-last-videos] - Latest Videos

Displays the latest videos from your PeerTube instance in a responsive grid.

#### Usage

```
[pt-last-videos]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `count` | number | 8 | Number of videos to display |
| `host_only` | true/false | true | Local videos only (not federated) |

#### Examples

**Standard view with 8 videos:**
```
[pt-last-videos]
```

**Show 12 videos:**
```
[pt-last-videos count="12"]
```

**All videos including federated videos:**
```
[pt-last-videos count="10" host_only="false"]
```

#### What is shown?

- Video thumbnail with duration overlay
- Video title (clickable)
- Length, Category, Publish Date, Views
- Sending responsibility (if any)
- Video number (if available)
- Tags (up to 5)

---

### [pt-latest-per-channel] - Latest videos per channel

Displays the latest video from each specified channel. Ideal for overview pages.

#### Usage

```
[pt-latest-per-channel]
```

or

```
[pt-latest-per-channel channels="kanal1,kanal2,kanal3"]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `channels` | Text | (from Settings) | Comma separated list of channel handles |

#### Examples

**Uses default channels from Settings:**
```
[pt-latest-per-channel]
```

**Specific Channels:**
```
[pt-latest-per-channel channels="ok_dessau,ok_magdeburg,okmq"]
```

#### Notes

- If no `channels` parameter is specified, the default channels from the plugin settings will be used
- Videos are sorted by publication date (newest first)
- Each channel is cached independently for better performance

---

### [pt-channel-videos] - All videos of a channel

Displays multiple videos from a specific channel.

#### Usage

```
[pt-channel-videos channel="kanal_handle"]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `channel` | Text | (required) | Channel handle |
| `count` | number | 6 | Number of videos |

#### Examples

**6 latest videos from OK MQ:**
```
[pt-channel-videos channel="okmq"]
```

**10 videos from OK Dessau:**
```
[pt-channel-videos channel="ok_dessau" count="10"]
```

#### Error messages

If the channel is not found or has no videos, a corresponding message will be displayed.

---

### [pt-video] - Single video with details

Displays a single video with full description and embedded player.

#### Usage

**By Video ID:**
```
[pt-video id="UUID"]
```

**By video number:**
```
[pt-video number="12345"]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `id` | Text | - | Video UUID or shortUUID |
| `number` | Text | - | Video number from plugin data |

**Important:** Either `id` or `number` must be specified!

#### Examples

**View video by ID:**
```
[pt-video id="xc86cB87iZXsgCofjHVcYJ"]
```

**View video by video number:**
```
[pt-video number="12345"]
```

#### What is shown?

- Embedded PeerTube player (16:9)
- Video title
- Full metadata:
  - Length
  - Category
  - Release date
  - Views
  - Sending responsibility
  - Video number
- All tags
- Full description (HTML formatted)
- “Watch on PeerTube” link

---

### [pt-search] - Search form

Displays a search form for PeerTube videos.

#### Usage

```
[pt-search]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `placeholder` | Text | "Search in the media library" | Placeholder text in search field |
| `action` | URL | (current page) | Destination URL for search |

#### Examples

**Simple search form:**
```
[pt-search]
```

**With custom placeholder:**
```
[pt-search placeholder="Search videos..."]
```

**With specific landing page:**
```
[pt-search action="/search results/"]
```

---

### [pt-search-results] - Search results

Displays search results with pagination. Should be on the same or a linked page as `[pt-search]`.

#### Usage

```
[pt-search-results]
```

#### Parameters

| Parameters | Type | Default | Description |
|-----------|-----|----------|--------------|
| `per_page` | number | 12 | Results per page |

#### Examples

**Standard view (12 videos):**
```
[pt-search-results]
```

**20 videos per page:**
```
[pt-search-results per_page="20"]
```

#### Setup example

**Page 1: "Search" (URL: /search/)**
```
[pt-search]
[pt-search-results]
```

**Page 2: "Search results" (URL: /search results/)**
```
[pt-search action="/search results/"]
```

Then go to `/search results/`:
```
[pt-search-results per_page="15"]
```

## Common problems

### Problem: No videos are displayed

**Solution 1: Check URL**
1. Go to `Settings > PeerTube Videos`
2. Check the PeerTube instance URL
3. Click on “Test connection”
4. Re-save the settings

**Solution 2: Clear cache**
1. Go to `Settings > PeerTube Videos`
2. Click “Clear Cache”
3. Refresh the videos page

**Solution 3: Check channel handle**
- Make sure the channel handle is spelled correctly
- Channel handles are case-sensitive!

### Problem: Videos are not updating

This is due to the caching system. To see new videos immediately:

1. Go to `Settings > PeerTube Videos`
2. Click “Clear Cache”
3. Refresh the page

**Or:** Reduce cache time in Settings.

### Problem: "Video not found" error

**For [pt-video id="..."]:**
- Check if the video ID is correct
- Make sure the video is public
- Check if the video exists on the specified instance

**For [pt-video number="..."]:**
- Check if the video has a video number
- The plugin searches up to 500 videos by number
- If there are more videos, the search might fail

### Problem: Search doesn't work

1. Check if both shortcodes exist:
   - `[pt-search]` for the form
   - `[pt-search-results]` for the results
2. Make sure search is enabled on the PeerTube instance
3. Clear the cache

### Problem: Slow loading times

**Short term solutions:**
- Increase cache time
- Reduce number of videos per page

**Long-term solutions:**
- Check connection speed to PeerTube instance
- Use a CDN for your WordPress site
- Enable page caching at the WordPress level

## Performance optimization tips

### 1. Optimal cache settings

**For frequently updated content:**
```
Videos: 5 minutes
Configuration: 24 hours
```

**For less frequently updated content:**
```
Videos: 15-30 minutes
Configuration: 48 hours
```

### 2. Sensible number of videos

- **Homepage:** 4-8 videos
- **Archive pages:** 12-16 videos
- **Channel Pages:** 6-12 videos

Too many videos on one page can increase loading time!

### 3. Using host_only

If you only want to show videos from your own instance:
```
[pt-last-videos host_only="true"]
```

This reduces the amount of data to be processed.

### 4. Use page caching

Use a caching plugin like:
- WP Super Cache
- W3 Total Cache
- WP Rocket

These cache the entire page and reduce the load significantly.

### 5. Image optimization

PeerTube's thumbnails will load automatically. Enable in your theme:
- Lazy loading (enabled by default in the plugin)
- WebP support in the browser

### 6. Regular cache cleaning

Schedule a regular cache cleanup:
- Daily for active pages
- Weekly for archives

You can automate this with a cron job.

### 7. API limits monitoring

By default, PeerTube allows:
- 50 requests per 10 seconds

The plugin automatically respects these limits through caching.

## Best practices

### Page structure

**Recommended structure:**

1. **Homepage:** Latest videos from all channels
   ```
   [pt-latest-per-channel]
   ```

2. **Media library page:** All videos with search
   ```
   [pt-search]
   [pt-last-videos count="16"]
   ```

3. **Channel Pages:** Dedicated page per channel
   ```
   [pt-channel-videos channel="ok_dessau" count="12"]
   ```

4. **Video Detail Pages:** Dynamic pages or posts
   ```
   [pt-video id="VIDEO_ID"]
   ```

### SEO tips

- Use descriptive page titles
- Add meta descriptions
- Use the video titles for H1 headings
- Create an XML sitemap with all video pages

### Accessibility

The plugin is designed to be barrier-free:
- Semantic HTML
- Alt texts for images
- ARIA labels where necessary
- Keyboard navigation possible

## Advanced customizations

### CSS adjustments

Add your own styles in your theme:

```css
/* Custom colors */
.pt-video-card:hover {
    border-color: #yourcolor;
}

/* Other grid layout */
.pt-video-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

/* Custom button style */
.pt-button-primary {
    background: #yourcolor;
    border-color: #yourcolor;
}
```

### Template overrides

Copy templates into your theme:

```
wp-content/themes/your-theme/peertube-video-manager/
├── video-card.php
├── video-detail.php
└── search-form.php
```

Then you can customize them as you wish.

## Support and updates

### Get updates

The plugin automatically checks for updates. Enable automatic updates in WordPress for seamless updates.

### Get help

1. Read this documentation
2. Check the FAQ in the README
3. Enable WP_DEBUG for detailed error messages
4. Create an issue on GitHub

### Enable debug mode

In `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Errors are then logged in `/wp-content/debug.log`.

## Summary

The PeerTube Video Manager Plugin provides a simple and powerful way to integrate PeerTube videos into WordPress. With the right settings and optimizations, you can have a fast, reliable video platform for your website.

**Good luck with your video portal!** 🎥