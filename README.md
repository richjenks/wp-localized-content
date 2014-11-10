# Localized Content

Show different content or redirect to another location based on user's location

## Usage

Localized Content has three shortcodes:

- **`[localized-text]`**: Show specific text
- **`[localized-include]`**: Include content from other post by ID or slug
- **`[localized-redirect]`**: Redirect to another location

To specify an action for a location, add its timezone as an attribute and the result as its value.

Timezone are case-insensitive but must use underscores instead of slashes (e.g. `America/New_York` becomes `America_New_York`) and refer to the [list of supported timezones](http://php.net/manual/en/timezones.php).

## Example

```
[localized-text Europe_London="Hi, London!" Europe="What's happening, Europe?" default="Hello, World!"]
```

In the example above, users in London will see the text "Hi, London!" and anyone anywhere else in Europe will see "What's happening, Europe?". All other users will see "Hello, World!". The `default` parameter is not required and if omitted and there are no matches, nothing will happen.

The first match will be the one used, so try to put the most specific timezone towards the start, e.g. `Europe_London` before `Europe`. Note that you do not need to specifiy an entire timezone, for example `Europe_L` will match Lisbon, Ljubljana, London and Luxembourg.

The `include` and `redirect` shortcodes work in exactly the same way.