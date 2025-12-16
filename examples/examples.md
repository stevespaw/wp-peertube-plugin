# Examples of PeerTube Video Manager

Here you will find practical examples of using the plugin in various scenarios.

## 1. Simple video library

Create a Videos page with all the latest videos.

### Setup
1. Create a new page: "Videos"
2. Paste this shortcode:

```
[pt-last-videos count="16"]
```

**Result:** A grid view with the 16 most recent videos from your PeerTube instance.

---

## 2. Channel overview page

View the latest video from each of your channels.

### Setup
1. Go to `Settings > PeerTube Videos`
2. Add your channels under "Standard Channels":
```
ok_dessau
ok_magdeburg
okmq
ok_merseburg
```
3. Create an “All Channels” page
4. Paste this shortcode:

```
[pt-latest-per-channel]
```

**Result:** Four cards with the latest video from each channel.

---

## 3. Dedicated Channel Page

Create a separate page for each channel with all videos.

### Setup
1. Create an “OK Dessau” page
2. Insert:

```html
<h1>OK Dessau - Open Canal Dessau</h1>
<p>Here you can find all videos from the Dessau Open Channel.</p>

[pt-channel-videos channel="ok_dessau" count="12"]
```

Repeat this for each channel with the appropriate channel handle.

---

## 4. Video details page (static)

Create a page for a specific video.

### Setup
1. Create a Featured Video Page
2. Insert:

```
[pt-video id="xc86cB87iZXsgCofjHVcYJ"]
```

**Result:** Full video view with player, description and all metadata.

---

## 5. Video details page (dynamic with video number)

Ideal for videos with video numbers from the peertube-plugin-okas-dev.

### Setup
1. Create a “Video” page
2. Insert:

```
[pt-video number="12345"]
```

**Usage:** Link to this page with different video numbers as parameters.

---

## 6. Search function

Add a search function for your video library.

### Setup A: Everything on one page

Create a Video Search page with:

```
<h1>Video search</h1>
[pt-search placeholder="Search for videos..."]

<h2>Search results</h2>
[pt-search-results per_page="12"]
```

### Setup B: Separate pages

**Page 1: "Search" (/search/)**
```
<h1>Video search</h1>
[pt-search action="/search results/"]
```

**Page 2: "Search results" (/search results/)**
```
<h1>Search results</h1>
[pt-search-results per_page="15"]
```

---

## 7. Homepage with featured videos

Show a small selection of videos on the homepage.

### Setup
On your homepage:

```html
<section class="featured-videos">
    <h2>Current videos</h2>
    [pt-last-videos count="4"]
    <p><a href="/videos/">View all videos »</a></p>
</section>
```

---

## 8. Sidebar widget with latest videos

Use the "HTML" widget in your sidebar.

### Setup
1. Go to `Design > Widgets`
2. Add a “Custom HTML” widget
3. Content:

```html
<h3>Latest Videos</h3>
[pt-latest-per-channel channels="ok_dessau,ok_magdeburg"]
```

**Note:** Consider adjusting the CSS for a more compact display.

---

## 9. Archive page with year filter

Combine multiple shortcodes for one archive.

### Setup
Create a "Video Archive 2024" page:

```html
<h1>Video archive 2024</h1>

<h2>All channels</h2>
[pt-latest-per-channel]

<hr>

<h2>OK Dessau</h2>
[pt-channel-videos channel="ok_dessau" count="8"]

<h2>OK Magdeburg</h2>
[pt-channel-videos channel="ok_magdeburg" count="8"]

<h2>OKMQ</h2>
[pt-channel-videos channel="okmq" count="8"]
```

---

## 10. Landing page for special event

Create a page for a specific video series or event.

### Setup
Page “Climate Change Theme Evening”:

```html
<h1>Theme evening: Climate Change</h1>
<p>A collection of our videos on the topic of climate change.</p>

[pt-search placeholder="Search more videos on this topic..."]
[pt-search-results per_page="8"]

<hr>

<h2>Recommended video</h2>
[pt-video id="ABC123XYZ"]
```

---

## 11. Multi-channel overview

Show videos from specific channels based on topic.

### Setup
Regional News Page:

```
<h1>Regional news</h1>
[pt-latest-per-channel channels="ok_dessau,ok_magdeburg,ok_merseburg"]
```

---

## 12. Responsive navigation

Use WordPress menus for channel navigation.

### Setup
1. Create one page per channel (see example 3)
2. Go to `Design > Menus`
3. Add all channel pages to the menu
4. Structure:
```
videos
├── All videos
├── Search
├── Channels
│ ├── OK Dessau
│ ├── OK Magdeburg
│ ├── OKMQ
│ └── OK Merseburg
```

---

## 13. Blog post with embedded video

Include a specific video in a blog post.

### Setup
In your blog post:

```html
<h2>Our latest video</h2>
<p>Check out our latest interview:</p>

[pt-video id="VIDEO_ID_HIER"]

<p>What do you think about the topic? Write it in the comments!</p>
```

---

## 14. Theme based collection

Use the search function for thematic collections.

### Setup
Documentation page:

```html
<h1>Documents</h1>
<p>All our documentaries in one place.</p>

<!-- User searches for “Documentation” -->
[pt-search placeholder="Search documentation..."]
[pt-search-results per_page="10"]
```

---

## 15. Multilingual site

Use WPML or Polylang for multilingual video sites.

### Setup
**German version (/de/videos/):**
```
<h1>Videos</h1>
[pt-last-videos count="12"]
```

**English version (/en/videos/):**
```
<h1>Videos</h1>
[pt-last-videos count="12"]
```

The video titles and descriptions come directly from PeerTube and retain their original language.

---

## Tips for all examples

### Performance
- Use `count` attributes wisely (not too high)
- Use caching plugins additionally
- Lazy loading is enabled by default

### Design
- Customize CSS in your theme
- Use theme builder (Elementor, etc.) for layout
- Test on different screen sizes

### SEO
- Add meta descriptions
- Use descriptive page titles
- Create a sitemap with all video pages

### Maintenance
- Clear cache after major changes
- Regularly check the connection to the PeerTube instance
- Update the plugin on new versions

---

## Combinations

You can also combine shortcodes for complex layouts:

```html
<div class="video-page">
    <section class="hero">
        <h1>Welcome to our media library</h1>
        [pt-search]
    </section>
    
    <section class="featured">
        <h2>Latest posts</h2>
        [pt-latest-per-channel]
    </section>
    
    <section class="all-videos">
        <h2>All videos</h2>
        [pt-last-videos count="12"]
    </section>
</div>
```

Then adjust the CSS for the desired layout.

---

## Support

If you have questions about these examples or need help implementing them:

1. Check the complete documentation
2. Check out the FAQ
3. Create an issue on GitHub

**Good luck with your video platform!** 🎬