# Localized Content

Show different content or redirect to another URL based on user's location

## Usage

Localized Content provides three shortcodes:

- **`[localized-text]`**: Show specific text
- **`[localized-include]`**: Include content from other post
- **`[localized-redirect]`**: Redirect to another location

To specify an action for a location, add its timezone as an attribute and the result as its value.

Timezone are case-insensitive but must use underscores instead of slashes (e.g. `America/New_York` becomes `America_New_York`) and refer to the [list of supported timezones](http://php.net/manual/en/timezones.php).

## Text

```
[localized-text Europe_London="Hi, London!" Europe="What's happening, Europe?" default="Hello, World!"]
```

Visitors in London will see "Hi, London!", visitors elsewhere in Europe will see "What's happening, Europe?" and everyone else will see default="Hello, World!".

The `default` parameter is not required and if omitted and there are no matches, nothing will happen.

The first match will be the one used, so try to put the most specific timezone towards the start, e.g. `Europe_London` before `Europe`. Note that you do not need to specifiy an entire timezone, for example `Europe_L` will match Lisbon, Ljubljana, London and Luxembourg.

## Include

```
[localized-include Europe_London="21" Europe="42" default="101"]
```

This example follows the same logic as the previous one but specified Post IDs instead of literal text.

This is especially powerful when you need multi-line content.

## Redirect

```
[localized-redirect Europe_London="https://google.co.uk" default="https://google.com"]
```

Visitors in London will be taken to Google's UK site and everyone else will be take to Google's global site.

## Testing

If you need to test content you can override the timezone with the `timezone` shortcode attribute.

```
[localized-text Europe_London="Hi, London!" Europe="What's happening, Europe?" default="Hello, World!" timezone="Europe_London"]
```

In the example above, the `Europe_London` timezone will be used, regardless of where the visitor is actually located.